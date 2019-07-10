<?php

use lajax\translatemanager\helpers\Language as Lx;
use yii\helpers\Url;

$this->title = Yii::t('titles', 'Signup | Angdocs');

?>

<section class="register bg pr">
    <div class="overlay-wrapper">
          <div class="overlay-left"></div>
        <div class="overlay-right"></div>
    </div>
    <div class="register-modal--wrapper">
        <div class="col-xs-12">
            <figure class="logo-wrapper--register append-form--desktop">
                <img class="img-responsive" src="<?= Yii::getAlias('@theme'); ?>/images/logo-login.png" title="" alt="">
            </figure>
            <div id="signup-form--div">
                <?= $this->render('/users/_form', [
                    'userForm' => $userForm,
                    'formId' => 'signup-form',
                    'buttonTitle' => 'Request Access',
                    'buttonClass' => 'login-btn'
                ]); ?>
            </div>
            <div class="register-request on-page">
                <div class="new-user"><?= Lx::t('frontend', 'have an account?'); ?></div>
                <a href="<?= Url::to("/site/login"); ?>" class="request-access"><?= Lx::t('frontend', 'login access'); ?></a>
            </div>
        </div>
    </div>
</section>

<?php

$signupJs = <<<JS
    function showOrHideForm() {
        if($(window).width() < 768) {
            $(".append-form--mobile").append($("#signup-form--div"));
        } else {
            $(".append-form--desktop").append($("#signup-form--div"));
        }
    }
    
    $(window).resize(function() {
        showOrHideForm();
    });
    
    showOrHideForm();
JS;

$this->registerJs($signupJs, $this::POS_READY);
