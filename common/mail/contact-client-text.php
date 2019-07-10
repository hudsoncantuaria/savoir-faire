<?php

/* @var $this yii\web\View */
/* @var $contactForm frontend\models\ContactForm */

?>

<?= Yii::t("email", "Hello"). ' ' . $contactForm->name; ?>,

<?= Yii::t("email/contact", "Thank you for contacting us. We will respond to you as soon as possible."); ?>
