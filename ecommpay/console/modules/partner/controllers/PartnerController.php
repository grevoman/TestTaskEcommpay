<?php

namespace console\modules\partner\controllers;

use common\modules\partner\models\Partner;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * Работа со списком партнёров
 */
class PartnerController extends Controller
{
    /**
     * Добавление нового партнёра
     *
     * @param string $name
     *
     * @return int
     */
    public function actionCreate(string $name): int
    {
        $model = new Partner();
        $model->name = $name;
        if (!$model->save()) {
            var_dump($model->errors);
            echo "Ошибка добавления партнёра\n";

            return ExitCode::UNSPECIFIED_ERROR;
        }
        echo "Партнёр добавлен успешно\n";

        return ExitCode::OK;
    }
}