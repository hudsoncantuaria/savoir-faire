<?php

use common\models\User;
use lajax\translatemanager\helpers\Language as Lx;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\ListView;
use yii\widgets\Pjax;

$this->title = Yii::t('titles', 'List Users | Angdocs');

$userTypeOptions = User::getTypeOptions();
$userStatusOptions = User::getStatusOptions();
$userStatusCssOptions = User::getStatusOptions(true);

$pageSizeOptions = [
    10 => 'List 10',
    25 => 'List 25',
    50 => 'List 50',
    100 => 'List 100',
];

Pjax::begin([
    'enablePushState' => false,
    'enableReplaceState' => false
]);

$form = ActiveForm::begin([
    'id' => 'filters-form',
    'action' => [''],
    'method' => 'get',
    'options' => [
        'data-pjax' => ''
    ]
]);

$userTypeInput = Html::dropDownList('type', $filters['type'], $userTypeOptions, [
    'class' => 'list-certificates form-control filter',
    'prompt' => 'All'
]);
$statusInput = Html::dropDownList('status', $filters['status'], $userStatusOptions, [
    'class' => 'list-certificates form-control filter',
    'prompt' => 'All'
]);
$pageSizeInput = Html::dropDownList('perPage', $filters['perPage'], $pageSizeOptions, [
    'class' => 'list-certificates form-control filter',
]);

// is logged user a manager?
$isManager = Yii::$app->user->identity->type == User::TYPE_MANAGER;
$roleFilterClass = !$isManager ? 'hidden' : '';

?>

    <div class="mobile-title visible-xs"><?= Lx::t('frontend', 'users'); ?></div>

    <div class="container-fluid">
        <div class="container">
            <div class="third-nav clearfix">
                <div class="hidden-xs col-md-3">
                    <div class="group search-group">
                        <div class="input-wrapper">
                            <label><?= Lx::t('frontend', 'Search Users'); ?></label>
                            <div class="relative">
                                <input type="text" name="search" id="search-filter" value="<?= $filters['search']; ?>">
                                <span class="search-icon">
                                    <i class="fa fa-search " aria-hidden="true"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="hidden-xs col-md-push-6 col-md-3">
                    <a href="<?= Url::to("/users/create"); ?>" class="certificates-btn">
                        <?= Lx::t('frontend', 'new user'); ?>
                        <i class="fa fa-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid bg-home table-content--desktop hidden-xs">
        <?php
        if (!empty($usersProvider->models)) {
            try {
                echo ListView::widget([
                    'dataProvider' => $usersProvider,
                    'layout' => '<div class="table-wrapper">
                                <div class="big-header hidden-xs">
                                    <div class="header-text--padding flex-between width-full">
                                        <div class="big-header--title"></div>
                                        <div class="select-wrapper display-flex">
                                            <div class="input-wrapper ' . $roleFilterClass . '">
                                                <label>' . Lx::t('frontend', 'Role') . '</label> ' . $userTypeInput . '
                                            </div>
                                            <div class="input-wrapper">
                                                <label>' . Lx::t('frontend', 'Status') . '</label> ' . $statusInput . '
                                            </div>
                                            <div class="input-wrapper">
                                            <label>&nbsp;</label>
                                            ' . $pageSizeInput . '
                                            </div>
                                        </div>
                                    </div>
                                    <div class="btn-wrapper view">
                                        <a class="view-all--history-btn" href="' . Url::to('/users/list') . '">
                                            ' . Lx::t('frontend', 'reset filters') . '
                                            <i class="fa fa-arrow-right" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="table">
                                    <div class="table-row header">
                                        <div class="cell title-cell text-uppercase id">' . Lx::t('frontend', 'ID') . '</div>
                                        <div class="cell title-cell text-uppercase">' . Lx::t('frontend', 'Username') . '</div>
                                        <div class="cell title-cell text-uppercase">' . ($isManager ? Lx::t('frontend', 'Company Name') : Lx::t('frontend', 'Name')) . '</div>
                                        <div class="cell title-cell text-uppercase">' . Lx::t('frontend', 'Email') . '</div>
                                        <div class="cell title-cell text-uppercase">' . Lx::t('frontend', 'Created At') . '</div>
                                        <div class="cell title-cell text-uppercase ' . $roleFilterClass . '">' . Lx::t('frontend', 'User Type') . '</div>
                                        <div class="cell title-cell text-uppercase">' . Lx::t('frontend', 'Status') . '</div>
                                    </div>
                                    {items}
                                </div>
                            </div>
                            <div class="flex-center">
                                <div class="pager-custom">{pager}</div>
                            </div>',
                    'pager' => [
                        'options' => [
                            'tag' => 'ul',
                            'class' => 'pager-custom--list clearfix',
                        ],
                    ],
                    'options' => [
                        'tag' => 'div',
                        'class' => 'col-md-12 bg-white',
                    ],
                    'itemOptions' => [
                        'class' => 'table-row'
                    ],
                    'itemView' => function ($item) use ($userTypeOptions, $userStatusOptions, $userStatusCssOptions, $isManager) {
                        return $this->renderAjax('_item', [
                            'item' => $item,
                            'userTypeOptions' => $userTypeOptions,
                            'userStatusOptions' => $userStatusOptions,
                            'userStatusCssOptions' => $userStatusCssOptions,
                            'isManager' => $isManager,
                        ]);
                    },
                ]);
            } catch (Exception $e) {
                echo 'An error has occurred loading the widget.';
            }
        } else { ?>
            <div class="col-md-12 bg-white">
                <div class="table-wrapper">
                    <div class="big-header hidden-xs">
                        <div class="header-text--padding flex-between width-full">
                            <div class="big-header--title"></div>
                            <div class="select-wrapper display-flex">
                                <div class="select-label-wrapper <?= $roleFilterClass; ?>">
                                    <label><?= Lx::t('frontend', 'Role'); ?></label>
                                    <?= $userTypeInput; ?>
                                </div>
                                <div class="select-label-wrapper">
                                    <label><?= Lx::t('frontend', 'Status'); ?></label>
                                    <?= $statusInput; ?>
                                </div>
                                <div class="select-label-wrapper">
                                    <label>&nbsp;</label>
                                    <?= $pageSizeInput; ?>
                                </div>
                            </div>
                        </div>
                        <div class="btn-wrapper view">
                            <a class="view-all--history-btn" href="<?= Url::to('/users/list'); ?>">
                                <?= Lx::t('frontend', 'reset filters'); ?>
                                <i class="fa fa-arrow-right" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                    <div class="table">
                        <div class="table-row header">
                            <div class="cell title-cell text-uppercase id"><?= Lx::t('frontend', 'ID'); ?></div>
                            <div class="cell title-cell text-uppercase"><?= Lx::t('frontend', 'Username'); ?></div>
                            <div class="cell title-cell text-uppercase"><?= ($isManager ? Lx::t('frontend', 'Company Name') : Lx::t('frontend', 'Name')); ?></div>
                            <div class="cell title-cell text-uppercase"><?= Lx::t('frontend', 'Email'); ?></div>
                            <div class="cell title-cell text-uppercase"><?= Lx::t('frontend', 'Created At'); ?></div>
                            <div class="cell title-cell text-uppercase <?= $roleFilterClass; ?>"><?= Lx::t('frontend', 'User Type'); ?></div>
                            <div class="cell title-cell text-uppercase"><?= Lx::t('frontend', 'Status'); ?></div>
                        </div>
                    </div>
                    <div class="no-results text-center">
                        <?= Lx::t('frontend', 'There are no results that match your search.'); ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <div class="table-content--mobile visible-xs">
        <?php
        try {
            echo ListView::widget([
                'dataProvider' => $usersProvider,
                'layout' => '<ul class="certificate-mobile--list clearfix">{items}</ul>
                            <div class="flex-center">
                                <div class="pager-custom">{pager}</div>
                            </div>',
                'pager' => [
                    'options' => [
                        'tag' => 'ul',
                        'class' => 'pager-custom--list clearfix',
                    ],
                ],
                'itemOptions' => [
                    'tag' => 'li',
                    'class' => ''
                ],
                'itemView' => function ($item) use ($userTypeOptions, $userStatusOptions, $userStatusCssOptions, $isManager) {
                    return $this->render('_item', [
                        'item' => $item,
                        'userTypeOptions' => $userTypeOptions,
                        'userStatusOptions' => $userStatusOptions,
                        'userStatusCssOptions' => $userStatusCssOptions,
                        'isManager' => $isManager,
                        'mobile' => true
                    ]);
                },
            ]);
        } catch (Exception $e) {
            echo 'An error has occurred loading the widget.';
        }
        ?>
    </div>

<?php
ActiveForm::end();
Pjax::end();

$filtersJs = <<<JS
    $(document).on('ready pjax:success', function(){
        $(".filter").change(function() {
            $("#filters-form").submit();
        });

        $("#search-filter").keypress(function(e) {
            if(e.which === 13){
                $("#filters-form").submit();
            }
        });
    });
JS;

$this->registerJs($filtersJs, $this::POS_READY);