<?php

namespace common\modules\partnerAnalytics\interfaces;

use Generator;

/**
 * Интерфейс парсера файлов аналитики по продажам партнеров
 */
interface AnalyticParserInterface
{
    /**
     * @return Generator
     */
    public function getData(): Generator;
}