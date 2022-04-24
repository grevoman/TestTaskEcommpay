<?php

namespace common\modules\partnerAnalytics\dto;

use DateTime;

/**
 * Параметры для формирования строки поиска файлов в Google Drive API
 */
class GoogleDriveSearchQueryDto
{
    /** @var int|null Количество запрашиваемых файлов */
    public ?int $count = null;

    /** @var string[] Массив MIME-типов */
    public array $mimeTypes = [];

    /** @var string Имя файла */
    public string $name = '';

    /** @var DateTime|null Время создания файла */
    public ?DateTime $createdTimeFrom = null;
}