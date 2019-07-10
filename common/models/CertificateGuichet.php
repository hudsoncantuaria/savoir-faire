<?php

namespace common\models;

use Yii;
use \common\models\base\CertificateGuichet as BaseCertificateGuichet;
use yii\helpers\ArrayHelper;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "certificate_guichet".
 */
class CertificateGuichet extends BaseCertificateGuichet {
    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created', 'modified'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['modified'],
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function rules() {
        return ArrayHelper::merge(
                parent::rules(), [
                # custom validation rules
                ]
        );
    }

}
