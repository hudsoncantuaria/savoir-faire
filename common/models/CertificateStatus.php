<?php

namespace common\models;

use \common\models\base\CertificateStatus as BaseCertificateStatus;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "certificate_status".
 */
class CertificateStatus extends BaseCertificateStatus {

    const PAGE_SIZE = 10;

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created']
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @param $idCertificate
     *
     * @return \yii\data\ActiveDataProvider
     */
    public static function getStatusProvider($idCertificate) {
        $query = self::find()->joinWith('user')->where(['id_certificate' => $idCertificate])->orderBy('created DESC');

        $dataProviderParams = ['query' => $query, 'pagination' => ['pageSize' => CertificateStatus::PAGE_SIZE]];

        $dataProvider = new ActiveDataProvider($dataProviderParams);

        return $dataProvider;
    }
}
