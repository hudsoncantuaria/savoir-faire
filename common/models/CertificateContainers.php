<?php

namespace common\models;

use Yii;
use \common\models\base\CertificateContainers as BaseCertificateContainer;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "certificate_container".
 */
class CertificateContainers extends BaseCertificateContainer
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
