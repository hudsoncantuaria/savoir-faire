<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $contactForm frontend\models\ContactForm */

?>

<div>
    <p><?= Yii::t("email", "Hello"). ' ' . Html::encode($contactForm->name); ?>,</p>
    <p><?= Yii::t("email/contact", "Thank you for contacting us. We will respond to you as soon as possible."); ?></p>
</div>
