 <?php

use common\models\User;
use lajax\translatemanager\helpers\Language as Lx;
use yii\helpers\Url;

$controller = Yii::$app->controller->id;
$action = Yii::$app->controller->action->id;

$user = Yii::$app->user->identity;
?>

<div class="container-fluid second-nav hidden-xs no-show-print">
    <div class="container <?= $action == 'list' ? 'nopadding-tab' : ''; ?>">
        <div class="second-nav">
            <nav class="col-xs-12 <?= $action == 'list' || ($controller == 'site' && $action == 'index') ? 'display-flex--sm-sb' : ''; ?>">
                <ul class="second-nav--list flex-it clearfix">
                    <li <?= $controller == 'certificates' || ($controller == 'site' && $action == 'index') ? 'class="active"' : ''; ?>>
                        <a href="<?= Url::home(); ?>"><?= Lx::t('frontend', 'certificates'); ?></a>
                    </li>
                    <?php if ($user->type == User::TYPE_MANAGER || ($user->type == User::TYPE_CLIENT && empty($user->id_user))) { ?>
                        <li <?= $controller == 'users' ? 'class="active"' : ''; ?>>
                            <a href="<?= Url::to("/users/list"); ?>"><?= $user->type == User::TYPE_MANAGER ? Lx::t('frontend', 'users management') : Lx::t('frontend', 'company users'); ?></a>
                        </li>
                    <?php } ?>
                    <li <?= $controller == 'documents' ? 'class="active"' : ''; ?>>
                        <a href="<?= Url::to("/documents/invoices"); ?>"><?= Lx::t('frontend', 'documents'); ?></a>
                    </li>
                    <?php if ($user->type == User::TYPE_MANAGER) { ?>
                        <li <?= $controller == 'translatemanager' ? 'class="active"' : ''; ?>>
                            <a href="<?= Url::to("/translatemanager/language/translate"); ?>">
                                <?= Lx::t('frontend', 'Translation Management'); ?>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
                <?php if ($controller == 'certificates' && in_array($user->type, [
                        User::TYPE_MANAGER,
                        User::TYPE_CLIENT
                    ])) { ?>
                    <div class="btn-wrapper visible-sm">
                        <a href="<?= Url::to("/certificates/create"); ?>" class="certificates-btn tab">
                            <?= Lx::t('frontend', 'new certificate'); ?>
                            <i class="fa fa-arrow-right" aria-hidden="true"></i>
                        </a>
                    </div>
                <?php } ?>
            </nav>
        </div>
    </div>
</div>
