<?php

namespace common\modules\partnerAnalytics\dto;

use yii\base\Model;

class AnalyticsDataDto extends Model
{
    /** @var \DateTime Дата и время заказа */
    public \DateTime $orderedAt;

    /** @var string Имя партнёра */
    public string $partner;

    /** @var string Наименование товара/услуги */
    public string $product;

    /** @var int Количество */
    public int $quantity;

    /** @var float Стоимость за единицу */
    public float $price;

    /** @var string Тип доставки (курьер/самовывоз) */
    public string $deliveryType;

    /** @var string|null Город доставки */
    public ?string $deliveryCity;

    /** @var float|null Стоимость доставки курьером */
    public ?float $deliveryCost;

    /** @var float Итого стоимость */
    public float $total;
}