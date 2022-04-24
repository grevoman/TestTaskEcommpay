<?php

namespace common\modules\partnerAnalytics\services;

use common\modules\partner\models\Partner;
use common\modules\partnerAnalytics\dto\AnalyticsDataDto;
use common\modules\partnerAnalytics\dto\GoogleDriveSearchQueryDto;
use common\modules\partnerAnalytics\interfaces\AnalyticParserInterface;
use common\modules\partnerAnalytics\interfaces\GoogleQueryBuilderInterface;
use common\modules\partnerAnalytics\interfaces\PartnerAnalyticsImportInterface;
use common\modules\partnerAnalytics\models\GoogleDriveFileModel;
use common\modules\partnerAnalytics\models\PartnerAnalyticData;
use DateTime;
use Exception;
use lhs\Yii2FlysystemGoogleDrive\GoogleDriveFilesystem;
use Yii;
use yii\helpers\FileHelper;

class GoogleDrivePartnerAnalyticsImportService implements PartnerAnalyticsImportInterface
{
    /** @var string Директория для хранения успешно обработанных отчётов */
    public string $directory_archive = 'storage/archive';

    /** @var string Директория для хранения отчётов с нарушенной структурой файла */
    public string $directory_invalid = 'storage/invalid';

    public array $errors = [];

    public string $logFileDir = '@runtime/logs/partnerAnalyticsImport/';

    protected GoogleDriveFilesystem $filesystem;
    protected GoogleQueryBuilderInterface $googleQueryBuilder;

    private $logFile = null;

    /**
     * @throws \yii\base\Exception
     * @throws \Exception
     */
    public function __construct(GoogleDriveFilesystem $filesystem, GoogleQueryBuilderInterface $googleQueryBuilder)
    {
        $this->filesystem = $filesystem;
        $this->googleQueryBuilder = $googleQueryBuilder;

        $logFileDir = Yii::getAlias($this->logFileDir);
        if (!FileHelper::createDirectory($logFileDir)) {
            throw new \Exception('Failed create log directory ' . $logFileDir);
        }
        $this->logFile = fopen(
            Yii::getAlias($this->logFileDir) . date('Ymd_His') . '.log',
            'w+b'
        );
    }

    /**
     * @throws \yii\base\Exception
     * @throws \Exception
     */
    public function import(GoogleDriveSearchQueryDto $searchQueryDto, DateTime $reportDate): bool
    {
        try {
            $this->createDirectories();

            while ($files = $this->filesystem->getAdapter()->getFilesList(
                $this->filesystem->rootFolderId,
                false,
                0,
                $this->googleQueryBuilder->getQuery($searchQueryDto)
            )) {
                unset($model);
                foreach ($files as $file) {
                    $model = new GoogleDriveFileModel();
                    $model->reportDate = $reportDate;
                    if (!$model->load($file, '') || !$model->validate()) {
                        continue;
                    }
                    fwrite(
                        $this->logFile,
                        sprintf("Обрабатывается файл %s партнёра %s\n", $model->name, $model->partnerName)
                    );

                    $partner = Partner::findOne(['name' => $model->partnerName]);
                    if ($partner === null) {
                        fwrite($this->logFile, sprintf("Партнёр с именем %s не найден в БД\n", $model->partnerName));

                        continue;
                    }

                    $isExistReport = PartnerAnalyticData::find()->where(
                        ['like', 'reportDate', $reportDate->format('Y-m-d')]
                    )->andWhere(['partnerId' => $partner->id])->exists();

                    if ($isExistReport) {
                        fwrite(
                            $this->logFile,
                            sprintf("Отчёт за дату %s уже существует\n", $reportDate->format('d.m.Y'))
                        );
                        continue;
                    }

                    $content = $this->filesystem->readStream($model->path);

                    if (!$tempFile = tempnam(sys_get_temp_dir(), 'google_drive_download_')) {
                        fwrite($this->logFile, "Ошибка создания временного файла\n");
                        throw new Exception('Ошибка создания временного файла');
                    }

                    if (!file_put_contents($tempFile, $content)) {
                        fwrite($this->logFile, "Ошибка сохранения во временный файл\n");
                        throw new Exception('Ошибка сохранения во временный файл');
                    }

                    /** @var AnalyticParserInterface $parser */
                    $parser = Yii::createObject(
                        [
                            'class' => AnalyticParserInterface::class,
                            'mimeType' => $model->mimetype,
                            'filename' => $tempFile,
                        ]
                    );

                    $total = 0;
                    try {
                        /** @var AnalyticsDataDto $data */
                        foreach ($parser->getData() as $data) {
                            $partnerAnalyticData = new PartnerAnalyticData();
                            $partnerAnalyticData->load($data->attributes, '');
                            $partnerAnalyticData->partnerId = $partner->id;
                            $partnerAnalyticData->reportDate = $reportDate;

                            if (!$partnerAnalyticData->validate()) {
                                throw new Exception('Отчёт содержит ошибку');
                            }

                            if (!$partnerAnalyticData->save()) {
                                throw new Exception('Ошибка при сохранении отчёта');
                            }

                            $total++;
                        }
                    } catch (Exception $exception) {
                        if (!rename($tempFile, $this->directory_invalid . '/' . $model->name . uniqid('', true))) {
                            throw new Exception('Ошибка переноса файла ' . implode(';', $file));
                        }

                        fwrite($this->logFile, "Обработано строк всего - " . $total . "\n");
                        continue;
                    }

                    fwrite($this->logFile, "Обработано строк всего - " . $total . "\n");
                    if (!rename($tempFile, $this->directory_archive . '/' . $model->name . uniqid('', true))) {
                        throw new Exception('Ошибка переноса файла ' . implode(';', $file));
                    }
                }
                if (isset($model->timestamp)) {
                    $searchQueryDto->createdTimeFrom = DateTime::createFromFormat(
                        'Y-m-d\TH:i:s.v\Z',
                        $model->createdTime
                    );
                }
            }
        } catch (Exception $exception) {
            $this->errors[] = $exception->getMessage();

            return false;
        }

        return true;
    }

    /**
     * @return void
     * @throws \yii\base\Exception
     * @throws \Exception
     */
    protected function createDirectories()
    {
        if (!FileHelper::createDirectory($this->directory_archive)) {
            throw new Exception('Ошибка создания директорий для хранения обработанных отчётов');
        }

        if (!FileHelper::createDirectory($this->directory_invalid)) {
            throw new Exception('Ошибка создания директорий для хранения отчётов с нарушенной структурой файла');
        }
    }
}