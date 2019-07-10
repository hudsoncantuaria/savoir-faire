<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace common\models\base;

use Yii;

/**
 * This is the base-model class for table "product_type".
 *
 * @property integer $id_product_type
 * @property string $created
 * @property string $modified
 * @property string $name
 *
 * @property \common\models\Certificate[] $certificates
 * @property string $aliasModel
 */
abstract class ProductType extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product_type';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created', 'name'], 'required'],
            [['created', 'modified'], 'safe'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_product_type' => Yii::t('models', 'Id Product Type'),
            'created' => Yii::t('models', 'Created'),
            'modified' => Yii::t('models', 'Modified'),
            'name' => Yii::t('models', 'Name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCertificates()
    {
        return $this->hasMany(\common\models\Certificate::className(), ['id_product_type' => 'id_product_type']);
    }


    
    /**
     * @inheritdoc
     * @return \common\models\query\ProductTypeQuery the active query used by this AR class.
     */
    /*public static function find()
    {
        return new \common\models\query\ProductTypeQuery(get_called_class());
    }*/


}