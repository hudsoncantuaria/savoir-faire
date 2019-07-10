<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */


?>

<div>
    <p style="font-weight: bold;"><?= Yii::t("email/credentials", "New Credentials"); ?></p>
    <p>
        <?php
        echo Yii::t("email/credentials", "Username:"). ' ' . Html::encode($user->username) . '<br>';
        echo Yii::t("email/credentials", "Password:"). ' ' . Html::encode($user->password) . '<br>';
        ?>
    </p>
</div>
