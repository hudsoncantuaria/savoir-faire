<?php

namespace common\models;

use Yii;
use \common\models\base\Doc as BaseDoc;
use yii\helpers\ArrayHelper;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "doc".
 */
class Doc extends BaseDoc {
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


    public function beforeValidate() {
        $this->name = preg_replace('/\s+/', '', $this->name);
        $this->path = preg_replace('/\s+/', '', $this->path);
        return parent::beforeValidate();
    }

}
