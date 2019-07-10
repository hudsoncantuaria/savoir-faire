<?php

/* @var $this yii\web\View */
/* @var $user common\models\User */

?>

<?= Yii::t("email/credentials", "New Credentials"); ?>,

<?= Yii::t("email/signup", "Username:"). ' ' . $user->username; ?>

<?= Yii::t("email/signup", "Password:"). ' ' . $user->password; ?>
