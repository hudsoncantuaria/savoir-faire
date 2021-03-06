<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base-model class for table "certificate_dus".
 *
 * @property integer $id_certificate_dus
 * @property integer $id_certificate
 * @property string $name
 * @property string $path
 * @property string $created
 * @property string $modified
 *
 * @property \common\models\Certificate $certificate
 * @property string $aliasModel
 */
abstract class CertificateDus extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'certificate_dus';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created',
                'updatedAtAttribute' => 'modified',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_certificate', 'name'], 'required'],
            [['id_certificate'], 'integer'],
            [['path'], 'string', 'max' => 255],
            [['name'], 'string', 'max' => 18],
            [['id_certificate'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\Certificate::className(), 'targetAttribute' => ['id_certificate' => 'id_certificate']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_certificate_dus' => Yii::t('models', 'Id Certificate Dus'),
            'id_certificate' => Yii::t('models', 'Id Certificate'),
            'created' => Yii::t('models', 'Created'),
            'modified' => Yii::t('models', 'Modified'),
            'name' => Yii::t('models', 'DU Number'),
            'path' => Yii::t('models', 'Path'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCertificate()
    {
        return $this->hasOne(\common\models\Certificate::className(), ['id_certificate' => 'id_certificate']);
    }


    
    /**
     * @inheritdoc
     * @return \common\models\CertificateDusQuery the active query used by this AR class.
     */
    /*
    public static function find()
    {
        return new \common\models\CertificateDusQuery(get_called_class());
    }
    */
}
