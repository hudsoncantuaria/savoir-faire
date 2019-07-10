<?php

namespace common\models;

use Yii;
use \common\models\base\TariffcodesCategory as BaseTariffcodesCategory;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tariffcodes_category".
 */
class TariffcodesCategory extends BaseTariffcodesCategory
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
