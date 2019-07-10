<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace common\models\base;

use Yii;

/**
 * This is the base-model class for table "tariffcodes_category".
 *
 * @property integer $id_tariffcodes_category
 * @property integer $id_class
 * @property string $code
 * @property string $description_en
 * @property string $description_pt
 *
 * @property \common\models\CertificateTariffcodes[] $certificateTariffcodes
 * @property \common\models\CertificateTariffcodes[] $certificateTariffcodes0
 * @property \common\models\TariffcodesClass $class
 * @property \common\models\TariffcodesProduct[] $tariffcodesProducts
 * @property string $aliasModel
 */
abstract class TariffcodesCategory extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tariffcodes_category';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_class', 'code', 'description_en', 'description_pt'], 'required'],
            [['id_class'], 'integer'],
            [['code'], 'string', 'max' => 2],
            [['description_en', 'description_pt'], 'string', 'max' => 255],
            [['id_class'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\TariffcodesClass::className(), 'targetAttribute' => ['id_class' => 'id_tariffcodes_class']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_tariffcodes_category' => Yii::t('models', 'Id Tariffcodes Category'),
            'id_class' => Yii::t('models', 'Id Class'),
            'code' => Yii::t('models', 'Code'),
            'description_en' => Yii::t('models', 'Description En'),
            'description_pt' => Yii::t('models', 'Description Pt'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCertificateTariffcodes()
    {
        return $this->hasMany(\common\models\CertificateTariffcodes::className(), ['code_tariffcodes_category' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCertificateTariffcodes0()
    {
        return $this->hasMany(\common\models\CertificateTariffcodes::className(), ['id_tariffcodes_category' => 'id_tariffcodes_category']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClass()
    {
        return $this->hasOne(\common\models\TariffcodesClass::className(), ['id_tariffcodes_class' => 'id_class']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTariffcodesProducts()
    {
        return $this->hasMany(\common\models\TariffcodesProduct::className(), ['id_category' => 'id_tariffcodes_category']);
    }




}
