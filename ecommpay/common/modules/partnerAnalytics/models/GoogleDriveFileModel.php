<?php

namespace common\modules\partnerAnalytics\models;

use common\modules\google\adapters\GoogleDriveAdvancedAdapter;
use common\modules\partner\models\Partner;
use DateTime;
use yii\base\Model;

/**
 * Модель файла, полученного от Google Drive
 */
class GoogleDriveFileModel extends Model
{
    private const FILENAME_PATTERN = '/^[\p{L},\s\d\'"-]+_(0[1-9]|[1-2][0-9]|3[0-1])\.(0[1-9]|1[0-2])\.(\d{4})/u';

    /** @var string The name of the file. */
    public string $name;

    /** @var string Directory or file. */
    public string $type;

    public string $path;

    public string $filename;

    public string $extension;

    /** @var int modifiedTime timestamp */
    public int $timestamp;

    public string $mimetype;

    public int $size;

    public string $createdTime;

    public ?DateTime $reportDate = null;

    public string $partnerName = '';

    public function rules(): array
    {
        return [
            [['name', 'type', 'path', 'filename', 'extension', 'mimetype', 'createdTime'], 'string'],
            [['timestamp', 'size'], 'integer'],
            [
                'mimetype',
                'in',
                'range' => [
                    GoogleDriveAdvancedAdapter::MIME_TYPE_CSV,
                    GoogleDriveAdvancedAdapter::MIME_TYPE_GOOGLE_SPREADSHEET,
                ],
            ],
            [
                'name',
                'match',
                'pattern' => self::FILENAME_PATTERN,
            ],
            [
                'name',
                'match',
                'pattern' => sprintf(
                    '/_%s./u',
                    $this->reportDate ? $this->reportDate->format('d.m.Y') : (new \DateTime())->format('d.m.Y')
                ),
            ],
            [
                'partnerName',
                'default',
                'value' => function () {
                    if (!$this->name) {
                        return '';
                    }
                    if (preg_match(Partner::NAME_PATTERN, $this->name, $matches) === 0) {
                        return '';
                    }

                    return $matches[0];
                },
            ],
        ];
    }
}