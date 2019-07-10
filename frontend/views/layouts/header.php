<?php

use lajax\translatemanager\widgets\ToggleTranslate;
use lajax\translatemanager\helpers\Language as Lx;
use yii\helpers\Html;
use yii\helpers\Url;

$controller = Yii::$app->controller->id;
$action = Yii::$app->controller->action->id;

?>
    <style type="text/css" media="print">
    .no-print { display: none; }
    </style>
    <header class="main-header clearfix no-show-print">
        <div class="container">
            <div class="col-md-6 col-xs-8">
                <div class="svg-wrapper">
                    <a href="<?= Url::home(); ?>"><img class="logo-header--mobile img-responsive" src="<?= Yii::getAlias('@theme'); ?>/images/logo.png" title="" alt=""></a>
                </div>
            </div>
            <div class="col-md-6 col-xs-4">
                <div class="burger-icon visible-xs visible-sm">
                    <i class="fa fa-bars" aria-hidden="true"></i>
                </div>
                <?php
                echo Html::beginForm('/site/change-language', 'post', ['id' => 'change-language']);
                ?>
                <div class="flex-it hidden-xs hidden-sm">
                    <a class="contacts-link" href="<?= Url::to(["/site/contacts"]); ?>">
                        <?= Lx::t('frontend', 'contacts') ?>
                    </a>
                    <?php
                    echo Html::dropDownList('language', Yii::$app->language, Yii::$app->params['languages'], ['class' => 'lang-select']);
                    ?>
                    <div class="user-login--wrapper">
                        <div class="selected">
                            <div class="user-name">
                                <?= Yii::$app->user->identity->username; ?>
                            </div>
                            <div class="user-company">
                                <?= Yii::$app->user->identity->name; ?>
                            </div>
                            <i class="fa fa-caret-down caret-icon" aria-hidden="true"></i>
                        </div>
                        <ul class="user-login--list hide">
                            <li>
                                <a href="<?= Url::to("/users/profile"); ?>"><?= Lx::t('frontend', 'edit profile'); ?></a>
                                <?php
                                try {
                                    echo ToggleTranslate::widget([
                                        'template' => '<a href="javascript:void(0);" id="toggle-translate" data-language="{language}" data-url="{url}"><i></i> {text}</a><div id="translate-manager-div"></div>',
                                        'frontendTranslationAsset' => 'lajax\translatemanager\bundles\FrontendTranslationAsset',
                                        'frontendTranslationPluginAsset' => 'lajax\translatemanager\bundles\FrontendTranslationPluginAsset',
                                    ]);
                                } catch (Exception $e) {
                                };
                                ?>
                                <a href="<?= Url::to("/site/logout"); ?>"><?= Lx::t('frontend', 'logout'); ?></a>
                            </li>
                        </ul>
                    </div>
                </div>
                <?php
                echo Html::endForm();
                ?>
            </div>
        </div>
    </header>

    <div class="menu-mobile--wrapper visible-xs visible-sm no-show-print">
        <div class="container-fluid">
            <div class="flex-between">
                <ul class="lang-mobile clearfix">
                    <li>
                        <?php foreach (Yii::$app->params['languages'] as $language) { ?>
                            <a <?= $language == Yii::$app->language ? 'class="active"' : ''; ?> href="<?= Url::to("/site/change-language?lang=" . $language); ?>"><?= $language; ?></a>
                        <?php } ?>
                    </li>
                </ul>
                <div class="flex-end">
                    <i class="fa fa-times close-menu--icon" aria-hidden="true"></i>
                </div>
            </div>
            <div class="user-info">
                <a href="<?= Url::to("/users/profile"); ?>">
                    <div class="user-name"><?= Yii::$app->user->identity->username; ?></div>
                    <div class="user-company"><?= Yii::$app->user->identity->name; ?></div>
                </a>
            </div>
            <?= $this->render('_menuMobile'); ?>
        </div>
        <a class="logout-mobile" href="<?= Url::to("/site/logout"); ?>"><?= Lx::t('frontend', 'logout'); ?></a>
    </div>

<?php
echo !in_array($action, ['contacts', 'profile']) && !($controller == 'users' && in_array($action, [
        'create',
        'update'
    ])) ? $this->render('_menu') : null;

$languageJs = <<<JS
$(".lang-select").change(function() {
    $("#change-language").submit();
});
JS;

$this->registerJs($languageJs, $this::POS_READY);
