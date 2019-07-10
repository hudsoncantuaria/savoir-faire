<?php

use common\models\CertificateStatus;
use lajax\translatemanager\helpers\Language as Lx;
use yii\widgets\ListView;

$this->title = Yii::t('titles', 'Certificate History');

$certificateStatusOptions = CertificateStatus::getStatusOptions();
$certificateStatusCssOptions = CertificateStatus::getStatusOptions(true);

echo $this->render('_menu', ['certificate' => $certificate]);
echo $this->render('_menuMobile', ['certificate' => $certificate]);

?>

<div class="container-fluid bg-history table-content--desktop hidden-xs">
    <div class="container">
        <?php
        if (!empty($certificateHistoryProvider->models)) {
            try {
                echo ListView::widget([
                    'dataProvider' => $certificateHistoryProvider,
                    'layout' => '
                            <div class="table-wrapper">
                                <div class="small-header">
                                    <div class="header-text--padding flex-between width-full">
                                        <div class="small-header--title">Certificate ' . $certificate->primaryKey . ' History</div>
                                    </div>
                                </div>
                                <div class="table small-table">
                                     <div class="table-row header">
                                        <div class="cell title-cell text-uppercase">date</div>
                                        <div class="cell title-cell text-uppercase">description</div>
                                        <div class="cell title-cell text-uppercase">user</div>
                                        <div class="cell title-cell text-uppercase">status</div>
                                    </div>
                                    {items} 
                                </div>
                            </div>
                            <div class="flex-center">
                                <div class="pager-custom">{pager}</div>
                            </div>
                        ',
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
                    'itemView' => function ($item) use ($certificateStatusOptions, $certificateStatusCssOptions) {
                        return $this->renderAjax('/certificates/_historyItem', [
                            'item' => $item,
                            'certificateStatusOptions' => $certificateStatusOptions,
                            'certificateStatusCssOptions' => $certificateStatusCssOptions
                        ]);
                    },
                ]);
            } catch (Exception $e) {
                echo 'An error has occurred loading the widget.';
            }
        } else { ?>
            <div class="col-md-12 bg-white">
                <div class="table-wrapper">
                    <div class="small-header">
                        <div class="header-text--padding flex-between width-full">
                            <div class="small-header--title">Certificate History <?= $certificate->primaryKey; ?></div>
                        </div>
                    </div>
                    <div class="table small-table">
                        <div class="table-row header">
                            <div class="cell title-cell text-uppercase">date</div>
                            <div class="cell title-cell text-uppercase">description</div>
                            <div class="cell title-cell text-uppercase">user</div>
                            <div class="cell title-cell text-uppercase">status</div>
                        </div>
                    </div>
                    <div class="no-results text-center">
                        <?= 'There are no results that match your search.'; ?>
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
            'dataProvider' => $certificateHistoryProvider,
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
            'itemView' => function ($item) use ($certificateStatusOptions, $certificateStatusCssOptions) {
                return $this->render('/certificates/_historyItem', [
                    'item' => $item,
                    'certificateStatusOptions' => $certificateStatusOptions,
                    'certificateStatusCssOptions' => $certificateStatusCssOptions,
                    'mobile' => true
                ]);
            },
        ]);
    } catch (Exception $e) {
        echo 'An error has occurred loading the widget.';
    }
    ?>
</div>