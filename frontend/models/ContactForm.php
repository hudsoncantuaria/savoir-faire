<?php

namespace frontend\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ContactForm extends Model {
    public $name;
    public $email;
    public $phone;
    public $description;


    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            // name, email, phone and description are required
            [['name', 'email', 'phone', 'description'], 'required'],
            // email has to be a valid email address
            ['email', 'email'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'description' => 'Description',
        ];
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     *
     * @return bool whether the email was sent
      */
    public function sendEmail() {
        // send email to client
        @Yii::$app->mailer->compose([
            'html' => "contact-client-html",
            'text' => "contact-client-text"
        ], ['contactForm' => $this])
            ->setFrom([Yii::$app->params['smtpEmail'] => 'Angdocs'])
            ->setTo($this->email)
            ->setSubject(Yii::t("email/contact", "Contact | Angdocs"))
            ->send();

        // send email to admin
        return Yii::$app->mailer->compose([
            'html' => "contact-admin-html",
            'text' => "contact-admin-text"
        ], ['contactForm' => $this])
            ->setFrom([Yii::$app->params['smtpEmail'] => 'Angdocs'])
            ->setTo(Yii::$app->params['contactEmail'])
            ->setSubject(Yii::t("email/contact", "Contact From User | Angdocs"))
            ->send();
    }
}
