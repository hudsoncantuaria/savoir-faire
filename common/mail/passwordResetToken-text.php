<?php

/* @var $this yii\web\View */
/* @var $user common\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);

?>

<?= Yii::t("email/resetPassword", "Hello") . ' ' . $user->username ?>,

<?= Yii::t("email/resetPassword", "Follow the link below to reset your password:"); ?>

<?= $resetLink ?>
