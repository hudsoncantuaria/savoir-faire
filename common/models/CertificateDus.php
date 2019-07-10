<?php

namespace common\models;

use Yii;
use \common\models\base\CertificateDus as BaseCertificateDus;
use yii\db\Expression;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "certificate_dus".
 */
class CertificateDus extends BaseCertificateDus
{
    
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created', 'modified'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['modified'],
                ],
                'value' => new Expression('NOW()'),
            ],
        ]);
    }
    
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [# custom validation rules
        ]);
    }
}
