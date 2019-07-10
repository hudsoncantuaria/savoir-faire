<?php

namespace common\models;

use Yii;
use \common\models\base\CertificateTranshipments as BaseCertificateTranshipments;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "certificate_transhipments".
 */
class CertificateTranshipments extends BaseCertificateTranshipments
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
