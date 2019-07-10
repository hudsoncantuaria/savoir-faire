<?php

namespace common\controllers;

use common\models\User;
use Yii;
use yii\web\Controller as BaseController;
use yii\web\Cookie;

class Controller extends BaseController {

    public function init() {
        // is logged user a manager or client top company?
        $user = !Yii::$app->user->isGuest ? Yii::$app->user->identity : null;
        Yii::$app->params['isManagerOrCompany'] = empty($user) || (!empty($user) && ($user->type == User::TYPE_MANAGER || empty($user->id_user)));
        
        // Force to get a language default for Language Manager
        if(!in_array(Yii::$app->language, ['en','pt']))
            $this->changelanguage('en');
    }
    
    public function actionChangeLanguage($lang = null) {
        $language = $lang != null ? $lang : Yii::$app->request->post('language');
        $this->changelanguage($language);
    }
    
    private function changelanguage($language){
        Yii::$app->language = $language;
        
        $languageCookie = new Cookie([
            'name' => 'language',
            'value' => $language,
            'expire' => time() + 60 * 60 * 24 * 30, // 30 days
        ]);
        
        Yii::$app->response->cookies->add($languageCookie);
        
        return $this->goHome();
    }
}
