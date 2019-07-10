<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace common\models\base;

use Yii;

/**
 * This is the base-model class for table "certificate_chassis".
 *
 * @property integer $id_certificate_chassi
 * @property integer $id_certificate
 * @property string $created
 * @property string $modified
 * @property string $nr
 * @property string $brand
 * @property string $model
 * @property string $description
 *
 * @property \common\models\Certificate $idCertificate
 * @property string $aliasModel
 */
abstract class CertificateChassis extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'certificate_chassis';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_certificate', 'nr', 'brand', 'model', 'description'], 'required'],
            [['id_certificate'], 'integer'],
            [['created', 'modified'], 'safe'],
            [['nr', 'brand', 'model'], 'string', 'max' => 20],
            [['description'], 'string', 'max' => 255],
            [['id_certificate'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\Certificate::className(), 'targetAttribute' => ['id_certificate' => 'id_certificate']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_certificate_chassi' => Yii::t('models', 'Id Chassi'),
            'id_certificate' => Yii::t('models', 'Id Certificate'),
            'created' => Yii::t('models', 'Created'),
            'modified' => Yii::t('models', 'Modified'),
            'nr' => Yii::t('models', 'Nr'),
            'brand' => Yii::t('models', 'Brand'),
            'model' => Yii::t('models', 'Model'),
            'description' => Yii::t('models', 'Content'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdCertificate()
    {
        return $this->hasOne(\common\models\Certificate::className(), ['id_certificate' => 'id_certificate']);
    }



    /**
     * @inheritdoc
     * @return \common\models\CertificateChassisActiveQuery the active query used by this AR class.
     */
    /*public static function find()
    {
        return new \common\models\CertificateChassisActiveQuery(get_called_class());
    }*/


}
