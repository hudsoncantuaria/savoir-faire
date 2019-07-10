<?php

namespace common\models;

use Yii;
use \common\models\base\Harbor as BaseHarbor;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "harbor".
 */
class Harbor extends BaseHarbor
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
