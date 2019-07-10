<?php

namespace common\models;

use Yii;
use \common\models\base\TariffcodesClass as BaseTariffcodesClass;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tariffcodes_class".
 */
class TariffcodesClass extends BaseTariffcodesClass
{

public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
             parent::rules(),
             [
                  # custom validation rules
             ]
        );
    }
}
