<?php

namespace common\behaviors;

use DateTime;
use yii\base\Event;

class TimestampBehavior extends \yii\behaviors\TimestampBehavior
{
    /**
     * @var string
     */
    public $createdAtAttribute = 'createdAt';

    /**
     * @var string
     */
    public $updatedAtAttribute = 'updatedAt';

    /**
     * @param Event $event
     *
     * @return string
     */
    protected function getValue($event): string
    {
        return $this->value ?: (new DateTime())->format('Y-m-d H:i:s');
    }
}
