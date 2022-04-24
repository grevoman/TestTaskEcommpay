<?php

namespace common\modules\partnerAnalytics\services\parsers;

use common\modules\partnerAnalytics\dto\AnalyticsDataDto;
use DateTime;
use Generator;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class SpreadsheetAnalyticParser extends AbstractAnalyticParser
{
    private const FIELD_ORDERED_AT = 1;
    private const FIELD_CLIENT = 2;
    private const FIELD_PRODUCT = 3;
    private const FIELD_QUANTITY = 4;
    private const FIELD_PRICE = 5;
    private const FIELD_DELIVERY_TYPE = 6;
    private const FIELD_DELIVERY_CITY = 7;
    private const FIELD_DELIVERY_COST = 8;
    private const FIELD_DELIVERY_TOTAL = 9;

    /**
     * @return Generator
     * @throws \Exception
     */
    public function getData(): Generator
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load($this->filename);
        $worksheet = $spreadsheet->getActiveSheet();

        $highestRow = $worksheet->getHighestRow();

        for ($row = 1; $row <= $highestRow; ++$row) {
            $analyticsData = new AnalyticsDataDto();

            $orderedAt = $worksheet->getCellByColumnAndRow(
                self::FIELD_ORDERED_AT,
                $row
            )->getFormattedValue();
            $orderedAt = DateTime::createFromFormat('d.m.Y H:i:s', $orderedAt);
            if ($orderedAt === false) {
                throw new \Exception('Невалидная дата в поле "orderedAt"');
            }
            $analyticsData->orderedAt = $orderedAt;

            $analyticsData->partner = $worksheet->getCellByColumnAndRow(
                self::FIELD_CLIENT,
                $row
            )->getValue();
            $analyticsData->product = $worksheet->getCellByColumnAndRow(
                self::FIELD_PRODUCT,
                $row
            )->getValue();
            $analyticsData->quantity = $worksheet->getCellByColumnAndRow(
                self::FIELD_QUANTITY,
                $row
            )->getValue();
            $analyticsData->price = $worksheet->getCellByColumnAndRow(
                self::FIELD_PRICE,
                $row
            )->getValue();
            $analyticsData->deliveryType = $worksheet->getCellByColumnAndRow(
                self::FIELD_DELIVERY_TYPE,
                $row
            )->getValue();
            $analyticsData->deliveryCity = $worksheet->getCellByColumnAndRow(
                self::FIELD_DELIVERY_CITY,
                $row
            )->getValue();
            $analyticsData->deliveryCost = $worksheet->getCellByColumnAndRow(
                self::FIELD_DELIVERY_COST,
                $row
            )->getValue();
            $analyticsData->total = $worksheet->getCellByColumnAndRow(
                self::FIELD_DELIVERY_TOTAL,
                $row
            )->getValue();

            yield $analyticsData;
        }
    }

}