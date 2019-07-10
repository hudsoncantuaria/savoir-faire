<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace common\models\base;

use Yii;

/**
 * This is the base-model class for table "container_type".
 *
 * @property integer $id_container_type
 * @property string $created
 * @property string $modified
 * @property string $name
 *
 * @property \common\models\CertificateContainer[] $certificateContainers
 * @property \common\models\CertificateContainerType[] $certificateContainerTypes
 * @property string $aliasModel
 */
abstract class ContainerTypes extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'container_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created', 'modified'], 'safe'],
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_container_type' => Yii::t('models', 'Id Container Type'),
            'created' => Yii::t('models', 'Created'),
            'modified' => Yii::t('models', 'Modified'),
            'name' => Yii::t('models', 'Name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCertificateContainers()
    {
        return $this->hasMany(\common\models\CertificateContainer::className(), ['id_container_type' => 'id_container_type']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCertificateContainerTypes()
    {
        return $this->hasMany(\common\models\CertificateContainerType::className(), ['id_container_type' => 'id_container_type']);
    }




}