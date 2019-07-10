<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $contactForm frontend\models\ContactForm */

?>

<?= Yii::t("email/contact", "Contact from User"); ?>

<?= Yii::t("email/contact", "Name:"). ' ' . $contactForm->name; ?>

<?= Yii::t("email/contact", "Phone:"). ' ' . $contactForm->phone; ?>

<?= Yii::t("email/contact", "Email:"). ' ' . $contactForm->email; ?>

<?= Yii::t("email/contact", "Description:"). ' ' . $contactForm->description; ?>
