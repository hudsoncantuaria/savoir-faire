<?php

use common\models\User;
use lajax\translatemanager\helpers\Language as Lx;
use yii\helpers\Url;

$controller = Yii::$app->controller->id;
$action = Yii::$app->controller->action->id;

?>

<ul class="mobile-items--list clearfix">
    <li <?= $controller == 'certificates' ? 'class="active"' : ''; ?>>
        <a href="<?= Url::home(); ?>">certificates</a>
    </li>
    <?php if (Yii::$app->user->identity->type == User::TYPE_MANAGER) { ?>
        <li <?= $controller == 'users' ? 'class="active"' : ''; ?>>
            <a href="<?= Url::to("/users/list"); ?>"><?= Lx::t('frontend', 'users management'); ?></a>
        </li>
    <?php } ?>
    <li <?= $controller == 'documents' ? 'class="active"' : ''; ?>>
        <a href="javascript: void(0);" class="coming-soon"><?= Lx::t('frontend', 'documents'); ?></a>
    </li>
    <li <?= $action == "contacts" ? 'class="active"' : ''; ?>>
        <a href="<?= Url::to("/site/contacts"); ?>"><?= Lx::t('frontend', 'contacts'); ?></a>
    </li>
</ul>