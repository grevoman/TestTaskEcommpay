<?php

namespace common\modules\partnerAnalytics\models;

use common\behaviors\TimestampBehavior;
use common\modules\partner\models\Partner;
use DateTime;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "partner_analytic_data".
 *
 * @property int $id
 * @property DateTime $orderedAt
 * @property int $partnerId
 * @property string $product
 * @property int $quantity
 * @property float $price
 * @property string $deliveryType
 * @property string|null $deliveryCity
 * @property float|null $deliveryCost
 * @property float $total
 * @property DateTime $reportDate
 * @property string $createdAt
 * @property string $updatedAt
 *
 * @property Partner $partnerRelation
 */
class PartnerAnalyticData extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%partner_analytic_data}}';
    }

    public function behaviors(): array
    {
        return [
            'TimestampBehavior' => TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'orderedAt',
                    'partnerId',
                    'product',
                    'quantity',
                    'price',
                    'deliveryType',
                    'total',
                    'reportDate',
                ],
                'required',
            ],
            [['orderedAt', 'reportDate', 'createdAt', 'updatedAt'], 'safe'],
            [['partnerId', 'quantity'], 'integer'],
            [['price', 'total', 'deliveryCost'], 'number'],
            [['product', 'deliveryType', 'deliveryCity'], 'string', 'max' => 255],
            [
                ['partnerId'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Partner::class,
                'targetAttribute' => ['partnerId' => 'id'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'orderedAt' => 'Ordered At',
            'partnerId' => 'Partner ID',
            'product' => 'Product',
            'quantity' => 'Quantity',
            'price' => 'Price',
            'deliveryType' => 'Delivery Type',
            'deliveryCity' => 'Delivery City',
            'deliveryCost' => 'Delivery Cost',
            'total' => 'Total',
            'reportDate' => 'Report Date',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
        ];
    }

    public function beforeSave($insert): bool
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->orderedAt = $this->orderedAt->format('Y-m-d H:i:s');
        $this->reportDate = $this->reportDate->format('Y-m-d H:i:s');

        return true;
    }

    public function afterFind(): void
    {
        $this->orderedAt = \DateTime::createFromFormat('Y-m-d H:i:s', $this->orderedAt);
        $this->reportDate = \DateTime::createFromFormat('Y-m-d', $this->reportDate);
    }

    /**
     * Gets query for [[Partner]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPartnerRelation(): ActiveQuery
    {
        return $this->hasOne(Partner::class, ['id' => 'partnerId']);
    }
}
