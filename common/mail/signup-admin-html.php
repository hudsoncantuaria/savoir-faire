<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */


?>

<div>
    <p style="font-weight: bold;"><?= Yii::t("email/signup", "New User Signup"); ?></p>
    <p>
        <?php
        echo Yii::t("email/signup", "Name:"). ' ' . Html::encode($user->name) . '<br>';
        echo Yii::t("email/signup", "Email:"). ' ' . Html::encode($user->email) . '<br>';
        ?>
    </p>
</div>
