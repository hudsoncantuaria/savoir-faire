<?php

use common\models\CertificateStatus;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $certificate common\models\Certificate */


switch ($certificate->last_status) {
    case CertificateStatus::STATUS_ACCEPTED:
        $emailText = Yii::t("email/certificateStatus", "The certificate was accepted.");
        break;
    case CertificateStatus::STATUS_INVOICE:
        $emailText = Yii::t("email/certificateStatus", "An invoice has been issued for the certificate.");
        break;
    case CertificateStatus::STATUS_EMITTED:
        $emailText = Yii::t("email/certificateStatus", "The certificate was emitted.");
        break;
    default:
        $emailText = "";
}

?>

<div>
    <p style="font-weight: bold;"><?= $emailText; ?></p>
    <p>
        <?php
        echo Yii::t("email/certificateStatus", "ID:"). ' ' . Html::encode($certificate->primaryKey) . '<br>';
        echo Yii::t("email/certificateStatus", "Company Name:"). ' ' . Html::encode($certificate->client->company->name) . '<br>';
        echo Yii::t("email certificateStatus", "Requester Name:"). ' ' . Html::encode($certificate->requester_name) . '<br>';
        ?>
    </p>
</div>
