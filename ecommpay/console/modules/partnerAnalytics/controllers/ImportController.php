<?php

namespace console\modules\partnerAnalytics\controllers;

use common\modules\google\adapters\GoogleDriveAdvancedAdapter;
use common\modules\partnerAnalytics\dto\GoogleDriveSearchQueryDto;
use common\modules\partnerAnalytics\interfaces\PartnerAnalyticsImportInterface;
use DateTime;
use Exception;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * Импорт аналитики по продажам от партнёров
 */
class ImportController extends Controller
{
    /**
     * @var string Дата, за которую импортируется отчёт в формате ДД.ММ.ГГГГ
     */
    public string $dateReport = '';

    protected PartnerAnalyticsImportInterface $service;

    public function __construct($id, $module, PartnerAnalyticsImportInterface $service, $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->service = $service;
    }

    public function options($actionID): array
    {
        return ['dateReport'];
    }

    public function optionAliases(): array
    {
        return ['d' => 'dateReport'];
    }

    /**
     * Запуск импорта аналитики по продажам от партнёров
     *
     * @return int
     * @throws \Exception
     */
    public function actionIndex(): int
    {
        $filter = new GoogleDriveSearchQueryDto();
        $filter->mimeTypes = [
            GoogleDriveAdvancedAdapter::MIME_TYPE_CSV,
            GoogleDriveAdvancedAdapter::MIME_TYPE_GOOGLE_SPREADSHEET,
        ];

        try {
            $reportDate = $this->getReportDate();
        } catch (Exception $exception) {
            echo $exception->getMessage() . "\n";

            return ExitCode::DATAERR;
        }

        if ($this->service->import($filter, $reportDate)) {
            echo "Импорт завершён успешно\n";

            return ExitCode::OK;
        }
        echo "Возникли ошибки при импорте\n";

        return ExitCode::UNSPECIFIED_ERROR;
    }

    /**
     * @return \DateTime
     * @throws \Exception
     */
    private function getReportDate(): DateTime
    {
        if (!$this->dateReport) {
            return new DateTime('today midnight');
        }

        if (!$dateReport = DateTime::createFromFormat('d.m.Y H:i', $this->dateReport . ' 00:00')) {
            throw new Exception('Формат даты должен быть ДД.ММ.ГГГГ');
        }

        return $dateReport;
    }
}