<?php

namespace common\modules\partnerAnalytics\interfaces;

use common\modules\partnerAnalytics\dto\GoogleDriveSearchQueryDto;

/**
 * Интерфейс для генерации строки поиска файлов и директорий в Google Drive API
 */
interface GoogleQueryBuilderInterface
{
    public function getQuery(GoogleDriveSearchQueryDto $filter): string;
}