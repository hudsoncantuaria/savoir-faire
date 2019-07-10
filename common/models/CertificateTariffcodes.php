<?php

namespace common\models;

use Yii;
use \common\models\base\CertificateTariffcodes as BaseCertificateTariffcodes;
use yii\helpers\ArrayHelper;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "certificate_tariffcodes".
 */
class CertificateTariffcodes extends BaseCertificateTariffcodes {

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


    public function beforeSave($insert) { $this->created = new Expression('NOW()');
        $this->modified = new Expression('NOW()');
        if (!parent::beforeSave($insert)) {
            return false;
        }
        
        

        $this->code_tariffcodes_category = str_pad($this->code_tariffcodes_category, 2, "0", STR_PAD_LEFT);
        $this->code_tariffcodes_class = str_pad($this->code_tariffcodes_class, 2, "0", STR_PAD_LEFT);
        $this->code_tariffcodes_product = str_pad($this->code_tariffcodes_product, 4, "0", STR_PAD_LEFT);
        $this->code = $this->code_tariffcodes_class . $this->code_tariffcodes_category . $this->code_tariffcodes_product;
        return true;
    }
}
