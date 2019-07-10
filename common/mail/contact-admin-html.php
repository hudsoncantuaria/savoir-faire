<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $contactForm frontend\models\ContactForm */

?>

<div>
    <p style="font-weight: bold;"><?= Yii::t("email/contact", "Contact from User"); ?></p>
    <p>
        <?php
        echo Yii::t("email/contact", "Name:"). ' ' . Html::encode($contactForm->name) . '<br>';
        echo Yii::t("email/contact", "Phone:"). ' ' . Html::encode($contactForm->phone) . '<br>';
        echo Yii::t("email/contact", "Email:"). ' ' . Html::encode($contactForm->email) . '<br>';
        echo Yii::t("email/contact", "Description:"). ' ' . Html::encode($contactForm->description) . '<br>';
        ?>
    </p>
</div>
