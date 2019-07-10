<?php

use common\models\Invoices;
use lajax\translatemanager\helpers\Language as Lx;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\ListView;
use yii\widgets\Pjax;

$this->title = Yii::t('titles', 'Documents - Invoices | Camdocs');

echo $this->render('/documents/_menu');

$invoicesStatusOptions = Invoices::getStatusOptions();
$invoicesStatusCssOptions = Invoices::getStatusOptions(true);

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

?>
    <div class="container-fluid bg-home table-content--desktop hidden-xs">
        <div class="container">
            <?php
            if (!empty($invoicesProvider->models)) {
                try {
                    echo ListView::widget([
                        'dataProvider' => $invoicesProvider,
                        'layout' => '<div class="table-wrapper">
                                        <div class="small-header">
                                            <div class="header-text--padding flex-between width-full">
                                                <div class="small-header--title">' . Lx::t('frontend', 'Invoices') . '</div>
                                            </div>
                                        </div>
                                        <div class="table small-table">
                                            <div class="table-row table-row--special-header">
                                                <div class="cell title-cell text-uppercase">' . Lx::t('frontend', 'Date') . '</div>
                                                <div class="cell title-cell text-uppercase">' . Lx::t('frontend', 'facture Nr') . '</div>
                                                <div class="cell title-cell text-uppercase">' . Lx::t('frontend', 'Value') . '</div>
                                                <div class="cell title-cell text-uppercase">' . Lx::t('frontend', 'Payment Informations') . '</div>
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
                            'class' => 'table-row table-row--special'
                        ],
                        'itemView' => function ($item) use ($invoicesStatusOptions, $invoicesStatusCssOptions) {
                            return $this->renderAjax('_item', [
                                'item' => $item,
                                'invoicesStatusOptions' => $invoicesStatusOptions,
                                'invoicesStatusCssOptions' => $invoicesStatusCssOptions,
                            ]);
                        },
                    ]);
                } catch (Exception $e) {
                    echo Lx::t('frontend', 'An error has occurred loading the widget.');
                }
            } else { ?>
                <div class="col-md-12 bg-white">
                    <div class="table-wrapper">
                        <div class="small-header">
                            <div class="header-text--padding flex-between width-full">
                                <div class="small-header--title"><?= Lx::t('frontend', 'Invoices'); ?></div>
                            </div>
                        </div>
                        <div class="table small-table">
                            <div class="table-row table-row--special-header">
                                <div class="cell title-cell text-uppercase"><?= Lx::t('frontend', 'Date'); ?></div>
	                            <div class="cell title-cell text-uppercase"><?= Lx::t('frontend', 'facture Nr'); ?></div>
	                            <div class="cell title-cell text-uppercase"><?= Lx::t('frontend', 'Value'); ?></div>
                            </div>
                        </div>
                        <div class="no-results text-center">
                            <?= Lx::t('frontend', 'There are no results that match your search.'); ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <div class="table-content--mobile visible-xs">
        <?php
        try {
            echo ListView::widget([
                'dataProvider' => $invoicesProvider,
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
                'itemView' => function ($item) use ($invoicesStatusOptions, $invoicesStatusCssOptions) {
                    return $this->render('_item', [
                        'item' => $item,
                        'invoicesStatusOptions' => $invoicesStatusOptions,
                        'invoicesStatusCssOptions' => $invoicesStatusCssOptions,
                        'mobile' => true
                    ]);
                },
            ]);
        } catch (Exception $e) {
            echo Lx::t('frontend', 'An error has occurred loading the widget.');
        }
        ?>
    </div>

<?php
ActiveForm::end();
Pjax::end();