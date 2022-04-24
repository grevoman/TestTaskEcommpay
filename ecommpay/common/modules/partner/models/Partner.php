<?php

namespace common\modules\partner\models;

use common\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "partner".
 *
 * @property int $id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 */
class Partner extends ActiveRecord
{
    public const NAME_PATTERN = '/^[\p{L},\s\d\'"-]+/u';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%partner}}';
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
            ['name', 'trim'],
            [['name'], 'required'],
            [
                'name',
                'match',
                'pattern' => self::NAME_PATTERN,
            ],
            [['createdAt', 'updatedAt'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'createdAt' => 'Создано',
            'updatedAt' => 'Обновлено',
        ];
    }
}
