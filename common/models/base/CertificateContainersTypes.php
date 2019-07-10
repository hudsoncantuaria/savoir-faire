<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace common\models\base;

use Yii;

/**
 * This is the base-model class for table "certificate_container_type".
 *
 * @property integer $id_certificate_container_type
 * @property integer $id_certificate
 * @property integer $id_container_type
 * @property string $created
 * @property string $modified
 * @property integer $packages_nr
 * @property string $volume
 * @property string $weight
 * @property string $freight
 *
 * @property \common\models\Certificate $certificate
 * @property \common\models\ContainerType $containerType
 * @property string $aliasModel
 */
abstract class CertificateContainersTypes extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'certificate_container_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_certificate', 'id_container_type'], 'required'],
            [['id_certificate', 'id_container_type', 'packages_nr'], 'integer'],
            [['created', 'modified'], 'safe'],
            [['vehicles_new', 'vehicles_used'], 'string'],
            [['volume', 'weight', 'freight', 'vehicles_contains'], 'number'],
            [['weight_unit'/*,'vehicles_contains'*/], 'boolean'],
            [['id_certificate'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\Certificate::className(), 'targetAttribute' => ['id_certificate' => 'id_certificate']],
            [['id_container_type'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\ContainerTypes::className(), 'targetAttribute' => ['id_container_type' => 'id_container_type']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_certificate_container_type' => Yii::t('models', 'Id Certificate Container Type'),
            'id_certificate' => Yii::t('models', 'Id Certificate'),
            'id_container_type' => Yii::t('models', 'Id Container Type'),
            'created' => Yii::t('models', 'Created'),
            'modified' => Yii::t('models', 'Modified'),
            'packages_nr' => Yii::t('models', 'Packages Nr'),
            'volume' => Yii::t('models', 'Volume'),
            'weight' => Yii::t('models', 'Weight'),
            'weight_unit' => Yii::t('models', 'Weight Unit'),
            'freight' => Yii::t('models', 'Freight'),
            'vehicles_contains' => Yii::t('models', 'Contains Vehicles'),
            'vehicles_new' => Yii::t('models', 'ID Numbers (one per line)'),
            'vehicles_used' => Yii::t('models', 'Vehicles Used'),
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
     * @return \yii\db\ActiveQuery
     */
    public function getContainerType()
    {
        return $this->hasOne(\common\models\ContainerTypes::className(), ['id_container_type' => 'id_container_type']);
    }
    
    public function getCertificateContainers() {
        return $this->hasMany(\common\models\CertificateContainers::className(), ['id_container_type' => 'id_container_type', 'id_certificate'=>'id_certificate'])->indexBy('id_certificate_container');
    }
    
    /*
     * @return $max_id_certificate_numbeer
     */
    public static function getMax(){
        $command = (new \yii\db\Query())
            ->select(['id_certificate_container', 'nr'])
            
            ->from('certificate_container')
            ->max("id_certificate_container");
        
        return $command;
    }
    
    
}