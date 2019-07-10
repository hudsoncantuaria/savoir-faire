<?php

namespace common\models\base;

use lajax\translatemanager\helpers\Language as Lx;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base-model class for table "user".
 *
 * @property integer $id
 * @property integer $id_user
 * @property string $id_country
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $delivery_place
 * @property string $name
 * @property string $address
 * @property string $city
 * @property string $postal_code
 * @property string $phone
 * @property string $fax
 * @property string $vat_code
 * @property integer $vat
 * @property integer $type
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property \common\models\Country $idCountry
 * @property string $aliasModel
 */
class User extends \yii\db\ActiveRecord {

    // user type
    const TYPE_MANAGER = 1;
    const TYPE_MAKER = 2;
    const TYPE_INVOICER = 3;
    const TYPE_CLIENT = 4;

    // user status
    const STATUS_ACTIVE = 1;
    const STATUS_PENDING = 2;
    const STATUS_CANCELED = 3;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'user';
    }


    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            [
                'class' => TimestampBehavior::className(),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['vat', 'status'], 'integer'],
            [
                [
                    'email',
                    'name',
                    'address',
                    'city',
                    'postal_code',
                    'phone',
                    'vat'
                ],
                'required'
            ],
            [
                ['username', 'password_hash', 'password_reset_token', 'email', 'name', 'address', 'city'],
                'string',
                'max' => 255
            ],
            [['delivery_place'], 'string', 'max' => 60],
            [['auth_key'], 'string', 'max' => 32],
            [['postal_code', 'phone', 'fax'], 'string', 'max' => 20],
            [['id_country'], 'string', 'max' => 2],
            [['vat'], 'unique'],
            [
                ['id_country'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Country::className(),
                'targetAttribute' => ['id_country' => 'cca2']
            ],
            [
                [
                    'id_user',
                    'vat_code',
                    'username',
                    'auth_key',
                    'password_hash',
                ],
                'safe'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('models', 'ID'),
            'id_user' => Yii::t('models', 'ID User External'),
            'id_country' => Yii::t('models', 'Id Countries'),
            'created_at' => Yii::t('models', 'Created At'),
            'updated_at' => Yii::t('models', 'Updated At'),
            'username' => Yii::t('models', 'Username'),
            'auth_key' => Yii::t('models', 'Auth Key'),
            'password_hash' => Yii::t('models', 'Password Hash'),
            'password_reset_token' => Yii::t('models', 'Password Reset Token'),
            'delivery_place' => Yii::t('models', 'Delivery Place'),
            'email' => Yii::t('models', 'Company Email'),
            'name' => Yii::t('models', 'Company Name'),
            'address' => Yii::t('models', 'Address'),
            'city' => Yii::t('models', 'City'),
            'postal_code' => Yii::t('models', 'Postal Code'),
            'phone' => Yii::t('models', 'Phone'),
            'fax' => Yii::t('models', 'Fax'),
            'vat' => Yii::t('models', 'Vat'),
            'type' => Yii::t('models', 'Role'),
            'status' => Yii::t('models', 'Status'),
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\UserQuery the active query used by this AR class.
     */
    public static function find() {
        return new \common\models\query\UserQuery(get_called_class());
    }

    /**
     * @return array
     */
    public static function getTypeOptions() {
        return [
            self::TYPE_MANAGER => 'Manager',
            self::TYPE_MAKER => 'Maker',
            self::TYPE_INVOICER => 'Invoicer',
            self::TYPE_CLIENT => 'Client'
        ];
    }

    /**
     * @param bool $cssClass
     *
     * @return array
     */
    public static function getStatusOptions($cssClass = false) {
        return !$cssClass ? [
            self::STATUS_ACTIVE => Yii::t('models', 'Active'),
            self::STATUS_PENDING => Yii::t('models', 'Pending'),
            self::STATUS_CANCELED => Yii::t('models', 'Canceled')
        ] : [
            self::STATUS_ACTIVE => 'green',
            self::STATUS_PENDING => 'blue',
            self::STATUS_CANCELED => 'red'
        ];
    }

    /**
     * @param bool $cssClass
     *
     * @return array
     */
    public static function getStatusPartialOptions($cssClass = false) {
        return !$cssClass ? [
            self::STATUS_ACTIVE => Yii::t('models', 'Active'),
            self::STATUS_CANCELED => Yii::t('models', 'Canceled')
        ] : [
            self::STATUS_ACTIVE => 'green',
            self::STATUS_CANCELED => 'red'
        ];
    }

}
