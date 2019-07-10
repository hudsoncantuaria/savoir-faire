<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;

/**
 * User form
 */
class UserForm extends Model {

    const EMAIL_TYPE_SIGNUP = 1;
    const EMAIL_TYPE_CREDENTIALS = 2;

    public $_rules;
    public $id;
    public $username;
    public $password;
    public $name;
    public $email;
    public $delivery_place;
    public $address;
    public $id_country;
    public $city;
    public $postal_code;
    public $phone;
    public $fax;
    public $vat_code;
    public $vat;
    public $type;
    public $status;
    public $id_user;

    /**
     * @inheritdoc
     */
    public function rules() {
        if (empty($this->_rules)) {
            $this->setRules();
        }

        return $this->_rules;
    }

    public function setRules($create = true) {
        $defaultRules = [
            ['name', 'string', 'max' => 255],
            ['email', 'trim'],
            ['email', 'email'],
            [['name', 'email'], 'string', 'max' => 255],
            ['username', 'trim'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['password', 'string', 'min' => 6],
        ];

        $requiredAndSafeRules = Yii::$app->params['isManagerOrCompany'] && empty($this->id_user) ? [
            [['name', 'email', 'address', 'id_country', 'city', 'postal_code', 'phone', 'vat'], 'required'],
            [['username', 'password', 'vat_code', 'type', 'status','fax','delivery_place', 'id_user'], 'safe'],
            ] : [
            [['name', 'email', 'address', 'id_country', 'city', 'postal_code', 'phone', 'id_user'], 'required'],
            [['username', 'password', 'vat_code', 'type', 'status', 'fax','delivery_place', 'vat'], 'safe'],
        ];

        $dynamicRules = $create ? [
            [
                'email',
                'unique',
                'targetClass' => '\common\models\User',
                'message' => 'This email address has already been taken.'
            ],
            [
                'username',
                'unique',
                'targetClass' => '\common\models\User',
                'message' => 'This username has already been taken.'
            ],
            ] : [];

        $this->_rules = array_merge($defaultRules, $requiredAndSafeRules, $dynamicRules);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'name' => Yii::$app->params['isManagerOrCompany'] && empty($this->id_user) ? Yii::t('models', 'Company Name') : Yii::t('models', 'Name'),
            'email' => Yii::$app->params['isManagerOrCompany'] && empty($this->id_user) ? Yii::t('models', 'Company Email') : Yii::t('models', 'Email'),
            'address' => Yii::t('models', 'Address'),
            'description' => Yii::t('models', 'Description'),
            'id_country' => Yii::t('models', 'Country'),
            'city' => Yii::t('models', 'City'),
            'postal_code' => Yii::t('models', 'Postal Code'),
            'phone' => Yii::t('models', 'Phone'),
            'fax' => Yii::t('models', 'Fax'),
            'delivery_place' => Yii::t('models', 'Delivery Place'),
            'vat_code' => Yii::t('models', 'VAT Code'),
            'vat' => Yii::t('models', 'VAT'),
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     * @throws \yii\base\Exception
     */
    public function create() {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->id_user = $this->id_user;
        $user->username = $this->username;
        if (!empty($this->password)) {
            $user->setPassword($this->password);
            $user->generateAuthKey();
        }
        $user->name = $this->name;
        $user->email = $this->email;
        $user->address = $this->address;
        $user->id_country = $this->id_country;
        $user->city = $this->city;
        $user->postal_code = $this->postal_code;
        $user->phone = $this->phone;
        $user->fax = $this->fax;
        $user->delivery_place = $this->delivery_place;
        $user->vat_code = $this->vat_code;
        $user->vat = $this->vat;
        $user->type = !empty($this->type) ? $this->type : User::TYPE_CLIENT;
        $user->status = !empty($this->status) ? $this->status : User::STATUS_PENDING;

        return $user->save() ? $user : null;
    }

    /**
     * Update user.
     *
     * @param array $disabledFields
     *
     * @return bool
     * @throws \yii\base\Exception
     */
    public function update($disabledFields = []) {
        if (!$this->validate()) {
            return false;
        }

        // get user and set updated attributes
        $user = !empty($this->id) ? User::findOne($this->id) : null;

        if ($user) {
            if (!empty($this->username)) {
                $user->username = !in_array('username', $disabledFields) ? $this->username : $user->username;
            }
            if (!empty($this->password)) {
                $user->setPassword($this->password);
                $user->generateAuthKey();
            }

            $user->name = !in_array('name', $disabledFields) ? $this->name : $user->name;
            $user->email = !in_array('email', $disabledFields) ? $this->email : $user->email;
            $user->address = !in_array('address', $disabledFields) ? $this->address : $user->address;
            $user->id_country = !in_array('id_country', $disabledFields) ? $this->id_country : $user->id_country;
            $user->city = !in_array('city', $disabledFields) ? $this->city : $user->city;
            $user->postal_code = !in_array('postal_code', $disabledFields) ? $this->postal_code : $user->postal_code;
            $user->phone = !in_array('phone', $disabledFields) ? $this->phone : $user->phone;
            $user->fax = !in_array('fax', $disabledFields) ? $this->fax : $user->fax;
            $user->delivery_place = !in_array('delivery_place', $disabledFields) ? $this->delivery_place : $user->delivery_place;
            $user->vat_code = !in_array('vat_code', $disabledFields) ? $this->vat_code : $user->vat_code;
            $user->vat = !in_array('vat', $disabledFields) ? $this->vat : $user->vat;
            $user->type = !empty($this->type) ? $this->type : $user->type;
            $user->status = !empty($this->status) ? $this->status : $user->status;

            return $user->save();
        }

        return false;
    }

    /**
     * @param string $type
     *
     * @return array|bool
     */
    public function sendEmail($type) {
        $user = User::findOne(['email' => $this->email]);

        if (!$user) {
            return false;
        }

        switch ($type) {
            case self::EMAIL_TYPE_SIGNUP:
                $subjectClient = Yii::t("email/signup", "Signup | Angdocs");
                $subjectAdmin = Yii::t("email/signup", "New User Signup | Angdocs");
                $templateType = 'signup';
                break;
            case self::EMAIL_TYPE_CREDENTIALS:
                $subjectClient = Yii::t("email/credentials", "New Credentials | Angdocs");
                $subjectAdmin = "";
                $templateType = 'credentials';
                break;
            default:
                $subjectClient = $subjectAdmin = Yii::t("email/default", "Angdocs");
                $templateType = 'default';
        }

        // send email to client
        @Yii::$app->mailer->compose([
                    'html' => "$templateType-client-html",
                    'text' => "$templateType-client-text"
                    ], ['user' => $this])
                ->setFrom([Yii::$app->params['smtpEmail'] => 'Angdocs'])
                // todo: uncomment
                //->setTo($this->email)
                ->setTo(Yii::$app->params['adminEmail'])
                ->setSubject($subjectClient)
                ->send();

        if ($type == self::EMAIL_TYPE_CREDENTIALS) {
            return ['message' => Yii::t('email/credentials', 'An email was sent to user with the new credentials.')];
        }

        // send email to admin
        return Yii::$app->mailer->compose([
                    'html' => "$templateType-admin-html",
                    'text' => "$templateType-admin-text"
                    ], ['user' => $user])
                ->setFrom([Yii::$app->params['smtpEmail'] => 'Angdocs'])
                ->setTo(Yii::$app->params['registerEmail'])
                ->setSubject($subjectAdmin)
                ->send();
    }

}
