<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace common\models\base;

use Yii;

/**
 * This is the base-model class for table "certificate_transhipments".
 *
 * @property integer $id_certificate_transhipment
 * @property integer $id_certificate
 * @property string $created
 * @property string $modified
 * @property string $ets
 * @property string $eta
 * @property integer $id_country
 * @property string $harbor
 * @property string $vessel
 *
 * @property \common\models\Certificate $idCertificate
 * @property \common\models\Country $idCountry
 * @property \common\models\Harbor $idHarbor
 * @property string $aliasModel
 */
abstract class CertificateTranshipments extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'certificate_transhipments';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_certificate', 'ets', 'eta', 'id_country', 'harbor', 'vessel'], 'required'],
            [['id_certificate'], 'integer'],
            [['created', 'modified', 'ets', 'eta'], 'safe'],
            [['vessel','harbor'], 'string', 'max' => 50],
            [['id_country'], 'string', 'max' => 2],
            [['id_certificate'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\Certificate::className(), 'targetAttribute' => ['id_certificate' => 'id_certificate']],
            [['id_country'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\Country::className(), 'targetAttribute' => ['id_country' => 'cca2']],
            //[['id_harbor'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\Harbor::className(), 'targetAttribute' => ['id_harbor' => 'id_harbor']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_certificate_transhipment' => Yii::t('models', 'Id Certificate Transhipment'),
            'id_certificate' => Yii::t('models', 'Id Certificate'),
            'created' => Yii::t('models', 'Created'),
            'modified' => Yii::t('models', 'Modified'),
            'ets' => Yii::t('models', 'Ets'),
            'eta' => Yii::t('models', 'Eta'),
            'id_country' => Yii::t('models', 'Id Country'),
            'harbor' => Yii::t('models', 'Harbor'),
            'vessel' => Yii::t('models', 'Vessel'),
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
     * @return \yii\db\ActiveQuery
     */
    public function getIdCountry()
    {
        return $this->hasOne(\common\models\Country::className(), ['id_country' => 'id_country']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    /*
    public function getIdHarbor()
    {
        return $this->hasOne(\common\models\Harbor::className(), ['id_harbor' => 'id_harbor']);
    }
    */


    /**
     * @inheritdoc
     * @return \common\models\CertificateTranshipmentsActiveQuery the active query used by this AR class.
     */
    /*public static function find()
    {
        return new \common\models\CertificateTranshipmentsActiveQuery(get_called_class());
    }*/


}