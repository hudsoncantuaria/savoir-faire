<?php

namespace frontend\controllers;

use common\controllers\Controller;
use common\models\User;
use frontend\models\UserForm;
use Helper\Functional;
use lajax\translatemanager\helpers\Language as Lx;
use Yii;
use yii\filters\AccessControl;

class UsersController extends Controller {

    public function init() {
        Lx::registerAssets();
        parent::init();
    }

    public function beforeAction($action) {
        if (($action->id != 'profile' && !in_array(Yii::$app->user->identity->type, [
                    User::TYPE_MANAGER,
                    User::TYPE_CLIENT
                ])) || !empty(Yii::$app->user->identity->id_user)) {
            $this->goHome();

            return false;
        }

        return parent::beforeAction($action);
    }

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['list'],
                'rules' => [
                    [
                        'actions' => ['list', 'profile'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Displays a list of all users.
     *
     * @param int $perPage
     * @param null $type
     * @param null $status
     * @param null $search
     *
     * @return mixed
     */
    public function actionList($perPage = User::PAGE_SIZE, $type = null, $status = null, $search = null) {
        // prepare filters
        $filters = [
            'perPage' => $perPage,
            'type' => $type,
            'status' => $status,
            'search' => $search,
        ];

        // get users provider
        $usersProvider = User::provider($perPage, $type, $status, $search);

        return $this->render('list', [
            'usersProvider' => $usersProvider,
            'filters' => $filters,
        ]);
    }

    /**
     * Create a user
     *
     * @return mixed
     * @throws \yii\base\Exception
     */
    public function actionCreate() {
        $userForm = new UserForm();
        $userForm->id_user = Yii::$app->user->identity->id;
        $userForm->setRules(true);
        if ($userForm->load(Yii::$app->request->post())) {
            if ($user = $userForm->create()) {
                // show a successful message.
                Yii::$app->getSession()->setFlash('success', [
                    'status' => 'success',
                    'message' => Lx::t('frontend', 'User successfully created.')
                ]);

                $this->redirect(["update?id={$user->primaryKey}"]);
            }
        }

        return $this->render('create', [
            'userForm' => $userForm
        ]);
    }

    /**
     * Update a user
     *
     * @return mixed
     * @throws \yii\base\Exception
     */
    public function actionUpdate($id) {
        // get user
        $user = User::findOne($id);
        
        $isChildAccounts = Yii::$app->user->identity->id == $user->id_user;
        $isManager = Yii::$app->user->identity->type == User::TYPE_MANAGER;
        
        if ($user && ($isChildAccounts || $isManager)) {
            // get actual user info
            $userUsername = $user->username;
            $userPassword = $user->password_hash;

            // load user form sand associate with user
            $userForm = new UserForm();
            $userForm->id = $user->primaryKey;
            $userForm->id_user = $user->id_user;
            $userForm->setRules(false);

            if ($userForm->load(Yii::$app->request->post()) && $userForm->update()) {
                // send email to user if credentials are updated and if user is active
                $validatePass = !empty($userForm->password) ? Yii::$app->security->validatePassword($userForm->password, $userPassword):false;
                if (Yii::$app->user->identity->type == User::TYPE_MANAGER &&
                    $userForm->status == User::STATUS_ACTIVE &&
                    ($userForm->username != $userUsername || !$validatePass)
                ) {
                    $sendEmailResult = $userForm->sendEmail($userForm::EMAIL_TYPE_CREDENTIALS);
                }

                // show a successful message.
                Yii::$app->getSession()->setFlash('success', [
                    'status' => 'success',
                    'message' => Lx::t('frontend', 'User successfully updated.') . (!empty($sendEmailResult['message']) ? '<br>' . $sendEmailResult['message'] : '')
                ]);
            } else {
                // set attributes from user into userForm
                $userForm->setAttributes($user->attributes);
            }

            return $this->render('update', [
                'userForm' => $userForm
            ]);
        }
        

        return $this->goHome();
    }

    /**
     * Update logged user profile
     *
     * @return mixed
     * @throws \yii\base\Exception
     */
    public function actionProfile() {
        // get user
        $user = User::findOne(Yii::$app->user->identity->getId());

        // load user form sand associate with user
        $userForm = new UserForm();
        $userForm->id = $user->primaryKey;
        $userForm->id_user = $user->id_user;
        $userForm->setRules(false);

        // set disabled fields (fields that the user can't change
        //        $disabledFields = $user->type != User::TYPE_MANAGER ? ['email'] : [];
        $disabledFields = null;

        if ($userForm->load(Yii::$app->request->post()) && $userForm->update()) {
            // show a successful message.
            Yii::$app->getSession()->setFlash('success', [
                'status' => 'success',
                'message' => Lx::t('frontend', 'Profile successfully updated.')
            ]);
        } else {
            // set attributes from user into userForm
            $userForm->setAttributes($user->attributes);
        }

        return $this->render('profile', [
            'userForm' => $userForm,
            'disabledFields' => $disabledFields
        ]);
    }

}
