<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */


?>

<div>
    <p><?= Yii::t("email", "Hello") . ' ' . Html::encode($user->name); ?></p>
</div>