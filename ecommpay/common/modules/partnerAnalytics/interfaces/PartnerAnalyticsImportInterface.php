<?php

namespace common\modules\partnerAnalytics\interfaces;

use common\modules\partnerAnalytics\dto\GoogleDriveSearchQueryDto;
use DateTime;

/**
 * Интерфейс для импорта аналитики по продажам
 */
interface PartnerAnalyticsImportInterface
{
    public function import(GoogleDriveSearchQueryDto $searchQueryDto, DateTime $reportDate): bool;
}