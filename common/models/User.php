<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\IdentityInterface;
use \common\models\base\User as BaseUser;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $delivery_place
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 *
 * @property \common\models\Certificate[] $certificates
 * @property \common\models\Invoices[] $invoices
 */
class User extends BaseUser implements IdentityInterface {

    const PAGE_SIZE = 10;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            ['status', 'default', 'value' => self::STATUS_PENDING],
            ['status', 'in', 'range' => [self::STATUS_PENDING, self::STATUS_ACTIVE, self::STATUS_CANCELED]],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id) {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null) {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     *
     * @return static|null
     */
    public static function findByUsername($username) {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by delivery place
     *
     * @param string $delivery_place
     *
     * @return static|null
     */
    public static function findByDeliveryPlace($delivery_place) {
        return static::findOne(['delivery_place' => $delivery_place, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     *
     * @return static|null
     */
    public static function findByPasswordResetToken($token) {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     *
     * @return bool
     */
    public static function isPasswordResetTokenValid($token) {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];

        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey() {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey) {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     *
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password) {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     *
     * @throws \yii\base\Exception
     */
    public function setPassword($password) {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey() {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken() {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken() {
        $this->password_reset_token = null;
    }

    /**
     * @param $type
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInvoices($type) {
        return $this->hasMany(Invoices::className(), [($type == self::TYPE_CLIENT ? 'id_user_client' : 'id_user_invoicer') => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry() {
        return $this->hasOne(Country::className(), ['id_country' => 'id_country']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCertificates($type) {
        return $this->hasMany(Certificate::className(), [($type == self::TYPE_CLIENT ? 'id_user_client' : ($type == self::TYPE_MAKER ? 'id_user_maker' : 'id_user_invoicer')) => 'id']);
    }

    /**
     * @param int $perPage
     * @param array $type
     * @param array $status
     * @param null $search
     * @param array $exceptionIds
     *
     * @return \yii\data\ActiveDataProvider
     */
    public static function provider($perPage = self::PAGE_SIZE, $type = [], $status = [], $search = null, $exceptionIds = []) {
        $query = self::find();

        if (!empty($search)) {
            $query->leftjoin('user ut', 'ut.id = user.id_user');
            $query->andFilterWhere(['like', 'ut.name', $search]);

            $query->orFilterWhere(['like', 'user.username', $search])
                ->orFilterWhere(['like', 'user.name', $search])
                ->orFilterWhere(['like', 'user.email', $search])
                ->orFilterWhere(['like', 'user.delivery_place', $search])
                ->orFilterWhere(['like', 'user.id', $search]);
        }


        if (!empty($exceptionIds)) {
            $query->andWhere(['not in', 'user.id', $exceptionIds]);
        }

        if (!empty($type)) {
            $query->andWhere(['in', 'user.type', $type]);
        }

        if (!empty($status)) {
            $query->andWhere(['in', 'user.status', $status]);
        }

        // filter by user ID for client type
        $user = Yii::$app->user->identity;
        if ($user->type == User::TYPE_CLIENT && empty($user->id_user)){
            $query->andWhere(['user.id_user' => $user->id]);
        }

        $query->orderBy('created_at DESC');

        $dataProviderParams = ['query' => $query, 'pagination' => ['pageSize' => $perPage]];

        $dataProvider = new ActiveDataProvider($dataProviderParams);

        return $dataProvider;
    }

    public function getCompany(){
        $parent = $this->hasOne(User::className(), ['id' => 'id_user'])->one();

        if(is_null($parent)){
            return $this;
        } else {
            return $parent;
        }
    }

}
