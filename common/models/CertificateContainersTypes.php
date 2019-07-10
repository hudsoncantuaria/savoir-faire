<?php

namespace common\models;

use Yii;
use \common\models\base\CertificateContainersTypes as BaseCertificateContainersTypes;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "certificate_container_type".
 */
class CertificateContainersTypes extends BaseCertificateContainersTypes
{
    // CONTAINS VEHICLES
    const CONTAINS_VEHICLES = [
        2 => "No",
        1 => "Yes",
    ];
    
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
