<?php

namespace common\models;

use Yii;
use \common\models\base\ProductType as BaseProductType;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "product_type".
 */
class ProductType extends BaseProductType
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
