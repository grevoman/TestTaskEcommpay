<?php

namespace common\modules\partnerAnalytics\services\parsers;

use common\modules\partnerAnalytics\dto\AnalyticsDataDto;
use DateTime;

class CsvAnalyticParser extends AbstractAnalyticParser
{
    private const FIELD_ORDERED_AT = 0;
    private const FIELD_CLIENT = 1;
    private const FIELD_PRODUCT = 2;
    private const FIELD_QUANTITY = 3;
    private const FIELD_PRICE = 4;
    private const FIELD_DELIVERY_TYPE = 5;
    private const FIELD_DELIVERY_CITY = 6;
    private const FIELD_DELIVERY_COST = 7;
    private const FIELD_DELIVERY_TOTAL = 8;

    /**
     * @return \Generator
     * @throws \Exception
     */
    public function getData(): \Generator
    {
        if (($handle = fopen($this->filename, "r")) !== false) {
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $analyticsData = new AnalyticsDataDto();
                $orderedAt = DateTime::createFromFormat('d.m.Y H:i:s', $data[self::FIELD_ORDERED_AT]);
                if ($orderedAt === false) {
                    throw new \Exception('Невалидная дата в поле "orderedAt"');
                }
                $analyticsData->orderedAt = $orderedAt;
                $analyticsData->partner = $data[self::FIELD_CLIENT];
                $analyticsData->product = $data[self::FIELD_PRODUCT];
                $analyticsData->quantity = $data[self::FIELD_QUANTITY];
                $analyticsData->price = $data[self::FIELD_PRICE];
                $analyticsData->deliveryType = $data[self::FIELD_DELIVERY_TYPE];
                $analyticsData->deliveryCity = $data[self::FIELD_DELIVERY_CITY];
                $analyticsData->deliveryCost = $data[self::FIELD_DELIVERY_COST];
                $analyticsData->total = $data[self::FIELD_DELIVERY_TOTAL];

                yield $analyticsData;
            }
            fclose($handle);
        }
    }

}