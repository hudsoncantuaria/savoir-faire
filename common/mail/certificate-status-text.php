<?php

use common\models\CertificateStatus;

/* @var $this yii\web\View */
/* @var $certificate common\models\Certificate */


switch ($certificate->last_status) {
    case CertificateStatus::STATUS_REJECTED:
        $emailText = Yii::t("email/certificateStatus", "The certificate was rejected.");
        break;
    case CertificateStatus::STATUS_ACCEPTED:
        $emailText = Yii::t("email/certificateStatus", "The certificate was accepted.");
        break;
    case CertificateStatus::STATUS_INVOICE:
        $emailText = Yii::t("email/certificateStatus", "An invoice has been issued for the certificate.");
        break;
    case CertificateStatus::STATUS_VALIDATED:
        $emailText = Yii::t("email/certificateStatus", "The certificate payment was validated.");
        break;
    case CertificateStatus::STATUS_EMITTED:
        $emailText = Yii::t("email/certificateStatus", "The certificate was emitted.");
        break;
    default:
        $emailText = "";
}

?>

<?= $emailText; ?>

<?= Yii::t("email/certificateStatus", "ID:"). ' ' . $certificate->primaryKey; ?>
<?= Yii::t("email/certificateStatus", "Company Name:"). ' ' . $certificate->client->company->name; ?>
<?= Yii::t("email/certificateStatus", "Requester Name:"). ' ' . $certificate->requester_name; ?>
