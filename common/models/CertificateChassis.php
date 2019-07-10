<?php

namespace common\models;

use Yii;
use \common\models\base\CertificateChassis as BaseCertificateChassis;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "certificate_chassis".
 */
class CertificateChassis extends BaseCertificateChassis
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
