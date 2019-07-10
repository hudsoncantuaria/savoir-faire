<?php

use common\helpers\CustomHelper;
use common\models\CertificateStatus;
use common\models\TariffcodesClass;
use kartik\typeahead\Typeahead;
use lajax\translatemanager\helpers\Language as Lx;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\models\base\User;


$user = Yii::$app->user->identity;

$makers = User::find()->where(["type" => User::TYPE_MAKER])->all();
$invoicers = User::find()->where(["type" => User::TYPE_INVOICER])->all();

$makersDropdown = [];
foreach ($makers as $maker) {
    $makersDropdown[$maker->primaryKey] = $maker->name;
}


$invoicersDropdown = [];
foreach ($invoicers as $invoicer) {
    $invoicersDropdown[$invoicer->primaryKey] = $invoicer->name;
}

$controller = Yii::$app->controller->id;
$action = Yii::$app->controller->action->id;

$disabledFields = !empty($disabledFields) ? $disabledFields : [];

$form = ActiveForm::begin([
    'id' => !empty($formId) ? $formId : '',
    'options' => [
        'class' => !empty($formClass) ? $formClass : '',
        'enctype' => 'multipart/form-data',
    ]
]);

if (count(Yii::$app->session->getAllFlashes()) > 0) {
    ?>
    <div class="generate-alerts margin-top-20">
        <?= CustomHelper::generateAlerts(); ?>
    </div>
    <?php
}


// STANDARD INPUT OPTIONS
$inputOptions = [
    'class' => 'group with-value',
    'tag' => 'div',
];

// STANDARD INPUT TEMPLATE
$inputTemplate = '{input}
                <span class="highlight"></span>
                <span class="bar"></span>
                {label}{error}';
// ARRAY INPUT TEMPLATE WITH DELETE ICON
$inputTemplateDelete = '{input}
                <span class="highlight"></span>
                <span class="bar"></span>
                {label}{error}<div class="icon-wrapper delete-item"><i class="fa fa-times" aria-hidden="true"></i></div>';

if (Yii::$app->controller->action->id == 'view') {
    $inputTemplateDelete = $inputTemplate;
}

// STANDARD INPUT FIELD OPTIONS
$inputFieldOptions = [
    'class' => '',
    'required' => '',
];
// NOT REQUIRED  INPUT FIELD OPTIONS
$inputNotRequiredFieldOptions = [
    'class' => '',
];

$readonlyFields = !empty($readonlyFields) && is_array($readonlyFields) ? $readonlyFields : [];


//CURRENCIES
$currencies = $certificate::CURRENCIES;
$costs = $certificate::COSTS;
//$containersTypes = [2 => "Dry", 1 => "Reefer"];
//$containersTypesFCLLCL = [1 => "FCL/FCL", 2 => "FCL/LCL", 3 => "LCL/FCL", 4 => "LCL/LCL"];

//TARIFFCODES - CLASS
$tariffcodesClasses = TariffcodesClass::find()->all();
$tariffcodesClassesItems = ArrayHelper::map($tariffcodesClasses, 'id_tariffcodes_class', 'description_pt');
$tariffcodesClassesOptions = ['prompt' => Yii::t('frontend/common', 'Escolha uma Classe')];

foreach ($tariffcodesClasses as $key => $class) {
    $tariffcodesClassesOptions[$class->id_tariffcodes_class] = ['data-code' => $class->code];
}

if ($action == 'update') {
    $formDocsOptions = [
        'class' => 'input-file',
    ];
} else {
    $formDocsOptions = [
        'class' => 'input-file',
        'required' => 'required',
    ];
}

$datatablesHiddenClass = $action == 'view' ? 'hidden' : '';
$weightContainers = 0;
$weightTariffcodes = 0;
$numContainers = 0;
?>

<?php
if ($action != 'view') {
    ?>
    <div class="clearfix">
        <div class="col-md-12 margin-top-20">
            <?php
            echo Html::submitButton('' . ($certificate->isNewRecord ? Lx::t('cruds', 'Create') : Lx::t('cruds',
                    'Save Certificate')), [
                'id' => 'save-' . $certificate->formName() . '-top',
                'class' => 'btn certificates-btn save pull-right',
            ]);
            ?>
        </div>
    </div>
    <?php
}
?>
    
    <div class="certificate-page--wrapper margin-top-20">
        <div class="certificate-box--item">
            <div class="certificate-box--head">
                <p><?= Lx::t('frontend', 'Status'); ?></p>
            </div>
            <div class="certificate-box--body">
                <div class="row">
                    <?php
                    echo $form->field($certificate, 'id_user_client')
                        ->hiddenInput(['value' => empty($certificate->id_user_client) ? Yii::$app->user->identity->id : $certificate->id_user_client])
                        ->label(false);
                    ?>
                    
                    <div class="col-xs-12 col-sm-4 certificate-box--column">
                        <?php
                        echo CustomHelper::generateInput($form, $certificate, 'requester_name', [
                            'class' => 'input-wrapper',
                            'tag' => 'div'
                        ], '{label}{input}',
                            $certificate->isNewRecord ? array_merge(['value' => Yii::$app->user->identity->name],
                                $inputFieldOptions) : $inputFieldOptions, $disabledFields);
                        ?>
                    </div>
                    <div class="col-xs-12 col-sm-4 certificate-box--column">
                        <?php
                        echo CustomHelper::generateInput($form, $certificate, 'requester_email', [
                            'class' => 'input-wrapper',
                            'tag' => 'div'
                        ], '{label}{input}',
                            $certificate->isNewRecord ? array_merge(['value' => Yii::$app->user->identity->email],
                                $inputFieldOptions) : $inputFieldOptions, $disabledFields);
                        ?>
                    </div>
                    <div class="col-xs-12 col-sm-4 certificate-box--column">
                        <div class="input-wrapper">
                            <label for="certificate-last-status"><?= Lx::t('frontend', 'City'); ?></label>
                            <?= CustomHelper::generateCitiesDropdownlist($form, $certificate, 'city',
                                ['id' => 'city-selection']); ?>
                        </div>
                    </div>
                    <?php
                    if (in_array($user->type, [
                            User::TYPE_MANAGER,
                            User::TYPE_MAKER,
                            User::TYPE_INVOICER
                        ]) && !$certificate->isNewRecord) {
                        ?>
                        <div class="col-xs-12 col-sm-4 certificate-box--column">
                            <div class="input-wrapper">
                                <label for="certificate-last-status"><?= Lx::t('frontend', 'Last Status'); ?></label>
                                <?= CustomHelper::generateLastStatusDropdownlist($form, $certificate, 'last_status', [
                                    'class' => 'certificate-currency',
                                    'id' => 'group-selection'
                                ]); ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    
                    
                    <?php
                    $user = Yii::$app->user->identity;
                    if (in_array($user->type, [
                            User::TYPE_MANAGER,
                        ]) && !$certificate->isNewRecord) {
                        ?>
                        <div class="col-xs-12 col-sm-4 certificate-box--column">
                            <div class="input-wrapper">
                                <label for="certificate-last-status"><?= Lx::t('frontend', 'Maker'); ?></label>
                                <?php echo CustomHelper::generateDropdownlist($form, $certificate, "id_user_maker",
                                    ['class' => 'certificate-currency'], $makersDropdown); ?>
                            </div>
                        </div>
                        
                        <div class="col-xs-12 col-sm-4 certificate-box--column">
                            <div class="input-wrapper">
                                <label for="certificate-last-status"><?= Lx::t('frontend', 'Invoicer'); ?></label>
                                <?php echo CustomHelper::generateDropdownlist($form, $certificate, "id_user_invoicer",
                                    ['class' => 'certificate-currency'], $invoicersDropdown); ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="certificate-page--wrapper margin-top-20">
        <div class="certificate-box--item">
            <div class="certificate-box--head">
                <p><?= Lx::t('frontend', 'Files'); ?> <?php
                    if (!empty($certificate->id_certificate)) {
                        ?>
                        <a href="<?= Url::to("/certificates/print?id={$certificate->primaryKey}"); ?>"
                           target="_blank"
                           class=""
                           style="color: #FFF">
                            <small><i class="fa fa-print"></i> <?= Yii::t('frontend', 'Form file Download') ?></small>
                        </a>
                        <?php
                    }
                    ?>
                </p>
            </div>
            <div class="certificate-box--body">
                <div class="row">
                    <div class="col-lg-4 col-sm-6 certificate-box--column">
                        <?php
                        $billLadingDownload = "";
                        if (!empty($certificate->docsBillLading->path)) {
                            $billLadingDownload = '<a href="/' . $certificate->docsBillLading->path . '" target="_blank"><i class="fa fa-cloud-download"></i></a>';
                        }
                        
                        echo $form->field($doc, 'upload_bill_lading', [
                            'options' => [
                                'class' => 'form-group to-valid-file',
                                'tag' => 'div',
                            ],
                            'template' => '{input}{label}{error}',
                        ])
                            ->fileInput(array_merge($formDocsOptions,
                                in_array('upload_bill_lading', $disabledFields) ? ['disabled' => ''] : []))
                            ->label('<span class="js-fileName">' . Lx::t('frontend',
                                    'Bill of Lading') . '</span><div><i class="icon fa fa-check icon-upload"></i>' . $billLadingDownload . '</div>',
                                ['class' => 'btn btn-tertiary js-labelFile']);
                        ?>
                    </div>
                    <div class="col-lg-4  col-sm-6 certificate-box--column">
                        <?php
                        $commercialInvoiceDownload = "";
                        if (!empty($certificate->docsCommercialInvoice->path)) {
                            $commercialInvoiceDownload = '<a href="/' . $certificate->docsCommercialInvoice->path . '" target="_blank"><i class="fa fa-cloud-download"></i></a>';
                        }
                        echo $form->field($doc, 'upload_commercial_invoice', [
                            'options' => [
                                'class' => 'form-group to-valid-file',
                                'tag' => 'div',
                            ],
                            'template' => '{input}{label}{error}',
                        ])
                            ->fileInput(array_merge($formDocsOptions,
                                in_array('upload_commercial_invoice', $disabledFields) ? ['disabled' => ''] : []))
                            ->label('<span class="js-fileName">' . Lx::t('frontend',
                                    'Commercial Invoice') . '</span><div><i class="icon fa fa-check icon-upload"></i>' . $commercialInvoiceDownload . '</div>',
                                ['class' => 'btn btn-tertiary js-labelFile']);
                        ?>
                    </div>
                    <div class="col-lg-4 col-sm-6 certificate-box--column">
                        <?php
                        $freightInvoiceDownload = "";
                        if (!empty($certificate->docsFreightInvoice->path)) {
                            $freightInvoiceDownload = '<a href="/' . $certificate->docsFreightInvoice->path . '" target="_blank"><i class="fa fa-cloud-download"></i></a>';
                        }
                        echo $form->field($doc, 'upload_freight_invoice', [
                            'options' => [
                                'class' => 'form-group to-valid-file',
                                'tag' => 'div',
                            ],
                            'template' => '{input}{label}{error}',
                        ])
                            ->fileInput(array_merge($formDocsOptions,
                                in_array('upload_freight_invoice', $disabledFields) ? ['disabled' => ''] : []))
                            ->label('<span class="js-fileName">' . Lx::t('frontend',
                                    'Freight Invoice') . '</span><div><i class="icon fa fa-check icon-upload"></i>' . $freightInvoiceDownload . '</div>',
                                ['class' => 'btn btn-tertiary js-labelFile']);
                        ?>
                    </div>
                    <div class="col-lg-4 col-sm-6 certificate-box--column">
                        <?php
                        $draftRequestSignedDownload = "";
                        if (!empty($certificate->docsDraftRequestSigned->path)) {
                            $draftRequestSignedDownload = '<a href="/' . $certificate->docsDraftRequestSigned->path . '" target="_blank"><i class="fa fa-cloud-download"></i></a>';
                        }
                        echo $form->field($doc, 'upload_draft_request_signed', [
                            'options' => [
                                'class' => 'form-group',
                                'tag' => 'div',
                            ],
                            'template' => '{input}{label}{error}',
                        ])
                            ->fileInput(array_merge(['class' => 'input-file'],
                                in_array('upload_new_invoice', $disabledFields) ? ['disabled' => ''] : []))
                            ->label('<span class="js-fileName">' . Lx::t('frontend',
                                    'Form file signed and stamped') . '</span><div><i class="icon fa fa-check icon-upload"></i>' . $draftRequestSignedDownload . '</div>',
                                ['class' => 'btn btn-tertiary js-labelFile']);
                        ?>
                        <?php
                        if (!empty($certificate->id_certificate)) {
                            ?>
                            <a href="<?= Url::to("/certificates/print?id={$certificate->primaryKey}"); ?>" target="_blank" class="certificate-box-column--btn"><?= Yii::t('frontend',
                                    'Form file Download') ?></a>
                            <?php
                        }
                        ?>
                    
                    
                    </div>
                    <div class="col-lg-4 col-sm-6 certificate-box--column">
                        <?php
                        $otherDownload = "";
                        if (!empty($certificate->docsOther->path)) {
                            $otherDownload = '<a href="/' . $certificate->docsOther->path . '" target="_blank"><i class="fa fa-cloud-download"></i></a>';
                        }
                        echo $form->field($doc, 'upload_other', [
                            'options' => [
                                'class' => 'form-group',
                                'tag' => 'div',
                            ],
                            'template' => '{input}{label}{error}',
                        ])
                            ->fileInput(array_merge(['class' => 'input-file'],
                                in_array('upload_new_invoice', $disabledFields) ? ['disabled' => ''] : []))
                            ->label('<span class="js-fileName">' . Lx::t('frontend',
                                    'Others') . '</span><div><i class="icon fa fa-check icon-upload"></i>' . $otherDownload . '</div>',
                                ['class' => 'btn btn-tertiary js-labelFile']);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="certificate-box--item margin-top-20">
        <div class="certificate-box--head">
            <p><?= Lx::t('frontend', 'Identification'); ?></p>
        </div>
        <div class="certificate-box--body">
            <div class="row">
                <div class="col-xs-12 col-sm-4 certificate-box--column">
                    <?php
                    echo CustomHelper::generateInput($form, $certificate, 'vessel_voyage_nr', [
                        'class' => 'input-wrapper to-valid',
                        'tag' => 'div'
                    ], '{label}{input}', [], $disabledFields);
                    ?>
                </div>
                <div class="col-xs-12 col-sm-4 certificate-box--column">
                    <?php
                    echo CustomHelper::generateInput($form, $certificate, 'goods_loading_date', [
                        'class' => 'input-wrapper datepicker to-valid',
                        'tag' => 'div'
                    ],
                        '{label}<div class="relative">{input}<span class="input-date--icon"><i class="fa fa-calendar" aria-hidden="true"></i></span></div>',
                        [
                            'data-date-format' => 'YYYY-MM-DD'
                        ], $disabledFields);
                    ?>
                </div>
                <div class="col-xs-12 col-sm-4 certificate-box--column">
                    <?php
                    echo CustomHelper::generateInput($form, $certificate, 'goods_deliveryestimate_date', [
                        'class' => 'input-wrapper datepicker to-valid',
                        'tag' => 'div'
                    ],
                        '{label}<div class="relative">{input}<span class="input-date--icon"><i class="fa fa-calendar" aria-hidden="true"></i></span></div>',
                        [
                            'data-date-format' => 'YYYY-MM-DD'
                        ], $disabledFields);
                    ?>
                </div>
                <div class="col-xs-12 col-sm-4 certificate-box--column">
                    <?php
                    echo CustomHelper::generateInput($form, $certificate, 'vessel_bl_nr', [
                        'class' => 'input-wrapper to-valid',
                        'tag' => 'div'
                    ], '{label}{input}', [], $disabledFields);
                    ?>
                </div>
                
                <div class="col-xs-12 col-sm-4 certificate-box--column">
                    <?php
                    echo CustomHelper::generateInput($form, $certificate, 'cost_invoice_value', [
                        'class' => 'input-wrapper to-valid',
                        'tag' => 'div'
                    ], '{label}{input}', [], $disabledFields);
                    ?>
                </div>
                <div class="col-xs-12 col-sm-4 certificate-box--column">
                    <div class="input-wrapper">
                        <label for="certificate-currency"><?= Lx::t('frontend', 'Goods Currency'); ?></label>
                        <?php echo CustomHelper::generateDropdownlist($form, $certificate, "currency",
                            ['class' => 'certificate-currency'], $currencies); ?>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-8 certificate-box--column">
                    <?php
                    echo CustomHelper::generateInput($form, $certificate, 'exporter_name', [
                        'class' => 'input-wrapper to-valid',
                        'tag' => 'div'
                    ], '{label}{input}', [], $disabledFields);
                    ?>
                </div>
	            <div class="col-xs-12 col-sm-4 certificate-box--column">
                    <?php
                    echo CustomHelper::generateInput($form, $certificate, 'forwarding_agent', [
                        'class' => 'input-wrapper to-valid',
                        'tag' => 'div'
                    ], '{label}{input}', [], $disabledFields);
                    ?>
	            </div>
                <div class="col-xs-12 col-sm-12 certificate-box--column">
                    <?php
                    echo CustomHelper::generateInput($form, $certificate, 'exporter_address', [
                        'class' => 'input-wrapper to-valid',
                        'tag' => 'div'
                    ], '{label}{input}', [], $disabledFields);
                    ?>
                </div>
                
                <div class="col-xs-12 certificate-box--column">
                    <div class="input-wrapper">
                        <label><?= Lx::t('frontend', 'Temporary DU'); ?></label>
                        <div class="file-input-wrapper" type="text">
                            <button type="button" class="add-file"><?= Lx::t('frontend', 'Add'); ?></button>
                            <?php
                            $countDus = 0;
                            $indexDu = 0;
                            foreach ($dus as $du) {
                                $indexDu = $du->primaryKey > $indexDu ? $du->primaryKey : $indexDu;
                                ?>
                                <div class="file-item" data-index="<?= $du->primaryKey ?>">
                                    <p class="file-name"><?= $du->name ?></p>
                                    <span><i class="fa fa-close" aria-hidden="true"></i></span>
                                    <div class="hidden">
                                        <?php
                                        echo CustomHelper::generateInput($form, $du, "[$du->primaryKey]name", [
                                            'class' => 'input-wrapper input-wrapper-name name',
                                            'tag' => 'div',
                                        ], '{label}{input}', [
                                            'class' => 'certificate-du--name',
                                        ], $disabledFields);
                                        echo $certificateDuFile = $form->field($certificateDus, "[$du->primaryKey]path",
                                            [
                                                'options' => [
                                                    'class' => 'input-wrapper file',
                                                    'tag' => 'div',
                                                ],
                                                'template' => '{label}{input}{error}',
                                            
                                            ])->fileInput(array_merge($formDocsOptions,
                                            in_array('upload_du', $disabledFields) ? [
                                                'disabled' => '',
                                                'class' => 'input-field certificate-du--path',
                                                'hiddenOptions' => [
                                                    'value' => $du->path,
                                                ]
                                            ] : [
                                                'class' => 'input-field certificate-du--path',
                                                'hiddenOptions' => [
                                                    'value' => $du->path,
                                                ]
                                            ]));
                                        ?>
                                        <div class="link">
                                            <a href="/<?= $du->path ?>" target="_blank"><?= $du->name ?></a>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            $indexDu++;
                            ?>
                        </div>
	                    <?php
	                    if(count($dus)>0) {
                            ?>
		                    <a href="<?= Url::to("/certificates/export-du?id_certificate={$certificate->primaryKey}"); ?>"
		                       target="_blank" class="certificate-box-column--btn"><?= Yii::t('frontend',
                                    "Exports Du's") ?></a>
                        <?php
                        }?>
                    </div>
                </div>
                <div class="file-modal--wrapper">
                    <div class="file-modal file-modal--du">
                        <span class="close-modal"><i class="fa fa-close" aria-hidden="true"></i></span>
                        <div class="row clearfix">
                            <div class="col-xs-12 certificate-box--column">
                                <p class="modal-title"><?= Lx::t('frontend', 'Upload File'); ?></p>
                            </div>
                            <div class="col-xs-12 certificate-box--column name"></div>
                            <div class="col-xs-12 certificate-box--column file"></div>
                            <div class="col-xs-12 certificate-box--column link-container"></div>
                            <div class="col-xs-12 certificate-box--column clearfix">
                                <button type="button" class="file-modal--submit"><?= Lx::t('frontend', 'Save'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="certificate-box--item margin-top-20">
        <div class="certificate-box--head">
            <p><?= Lx::t('frontend', 'Importer'); ?></p>
        </div>
        <div class="certificate-box--body">
            <div class="row">
                <div class="col-xs-12 col-sm-4 certificate-box--column">
                    <?php
                    echo CustomHelper::generateInput($form, $certificate, 'importer_vat', [
                        'class' => 'input-wrapper to-valid',
                        'tag' => 'div'
                    ], '{label}{input}', [], $disabledFields);
                    ?>
                </div>
                <div class="col-xs-12 col-sm-8 certificate-box--column">
                    <?php
                    echo CustomHelper::generateInput($form, $certificate, 'importer_name', [
                        'class' => 'input-wrapper to-valid',
                        'tag' => 'div'
                    ], '{label}{input}', [], $disabledFields);
                    ?>
                </div>
                <div class="col-xs-12 col-sm-12 certificate-box--column">
                    <?php
                    echo CustomHelper::generateInput($form, $certificate, 'importer_address', [
                        'class' => 'input-wrapper to-valid',
                        'tag' => 'div'
                    ], '{label}{input}', [], $disabledFields);
                    ?>
                </div>
                <div class="col-xs-12 col-sm-4 certificate-box--column">
                    <div class="input-wrapper">
                        <label for="certificate-currency"><?= Lx::t('frontend', 'Credit Letter'); ?></label>
                        <?php echo CustomHelper::generateDropdownlist($form, $certificate, "importer_cc",
                            ['class' => 'certificate-currency'], [
                                '1' => Yii::t('frontend', 'Yes'),
                                '2' => Yii::t('frontend', 'No')
                            ]); ?>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-8 certificate-box--column">
                    <?php
                    echo CustomHelper::generateInput($form, $certificate, 'importer_bank', [
                        'class' => 'input-wrapper',
                        'tag' => 'div'
                    ], '{label}{input}', [], $disabledFields);
                    ?>
                </div>
            </div>
        </div>
        <div class="certificate-box--item margin-top-20">
            <div class="certificate-box--head">
                <p><?= Lx::t('frontend', 'Shipment Information'); ?></p>
            </div>
            <div class="certificate-box--body">
                <div class="row">
                    <div class="col-xs-12 col-sm-6 certificate-box--column">
                        <?php
                        echo CustomHelper::generateInput($form, $certificate, 'vessel_name', [
                            'class' => 'input-wrapper to-valid',
                            'tag' => 'div'
                        ], '{label}{input}', [], $disabledFields);
                        ?>
                    </div>
                    <div class="col-xs-12 col-sm-6 certificate-box--column">
                        <?php
                        echo CustomHelper::generateInput($form, $certificate, 'goods_loading_harbor', [
                            'class' => 'input-wrapper',
                            'tag' => 'div'
                        ], '{label}{input}', [], $disabledFields);
                        ?>
                    </div>
                    <div class="col-xs-12 col-sm-6 certificate-box--column">
                        <?php
                        echo CustomHelper::generateInput($form, $certificate, 'vessel_shipping_line', [
                            'class' => 'input-wrapper to-valid',
                            'tag' => 'div'
                        ], '{label}{input}', [], $disabledFields);
                        ?>
                    </div>
                    <div class="col-xs-12 col-sm-6 certificate-box--column">
                        <?php
                        echo CustomHelper::generateInput($form, $certificate, 'goods_destination_harbor', [
                            'class' => 'input-wrapper',
                            'tag' => 'div'
                        ], '{label}{input}', [], $disabledFields);
                        ?>
                    </div>
                    <div class="col-xs-12 col-sm-12 certificate-box--column">
                        
                        <?php
                        echo $form->field($certificate, 'goods_description', [
                            'options' => ['class' => 'input-wrapper'],
                            'template' => '{label}{input}',
                        ])->textArea(array_merge([
                            'rows' => 3,
                            'id' => 'comment',
                        ], in_array('goods_description', $disabledFields) ? ['disabled' => ''] : []));
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="certificate-box--item margin-top-20">
        <div class="certificate-box--head">
            <p><?= Lx::t('frontend', 'Stowage'); ?></p>
        </div>
        <div class="certificate-box--body clearfix">
            <?php
            if ($action != 'view' && count($containersTypes) > 0) {
                ?>
                <div class="new-container--wrapper margin-top-20">
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                            <i class="fa fa-plus"></i>
                            <span><?= Lx::t('frontend', 'Add Stowage')?></span>
                            <i class="fa fa-caret-down"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <?php
                            foreach ($addContainerTypes as $key => $value) {
                                ?>
                                <li>
                                    <a href="#" data-container-type-id="<?= $key ?>" data-container-type-name="<?= $value ?>"><?= $value ?></a>
                                </li>
                                <?
                            }
                            ?>
                        </ul>
                    </div>
                </div>
                <?php
            }
            ?>
            
            <div class="container-list">
                <?php
                foreach ($containersTypes as $certificateContainersType) {
                    ?>
                    <div class="container-wrapper open" container-type-id="<?= $certificateContainersType->id_container_type ?>">
                        <div class="container-head">
                            <div class="container-head--title">
                                <i class="fa fa-cube"></i>
                                <p><?= $certificateContainersType->containerType->name ?></p>
                            </div>
                            <div class="container-head--arrow">
                                <i class="fa fa-angle-double-down"></i>
                            </div>
                        </div>
                        <div class="container-body">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-4 certificate-box--column">
                                    <div class="container-body--group">
                                        <div class="container-group--head">
                                            <input type="hidden" name="table-container-type" class="table-container-type" value="<?= $certificateContainersType->id_container_type ?>">
                                            <p><?= Lx::t('frontend', 'Container IDs '); ?><span><?= Lx::t('frontend',
                                                        '(one per line)'); ?></span></p>
                                            <div class="btns-wrapper">
                                                <?php
                                                if ($action != 'view') {
                                                    ?>
                                                    <button class="container-table--add" title="<?= Yii::t('frontend',
                                                        'Add one container'); ?>">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                    <button class="container-table--addGroup" title="<?= Yii::t('frontend',
                                                        'Add multiple containers'); ?>">
                                                        <i class="fa fa-plus-square"></i>
                                                    </button>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="container-group--body table-group">
                                            <div class="input-wrapper">
                                                <input type="text" id="container--table-search" class="container--table-search">
                                            </div>
                                            <div class="container-table--wrapper">
                                                <table class="container-table">
                                                    <tr class="container-table--head">
                                                        <td></td>
                                                        <td>
                                                            <p><?= Lx::t('frontend', 'Container ID'); ?></p>
                                                        </td>
                                                        <td>
                                                            <p><?= Lx::t('frontend', 'SOC'); ?></p>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                    foreach ($certificateContainersType->certificateContainers as $container) {
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <?php
                                                                if ($action != 'view') {
                                                                    ?>
                                                                    <span class="container-table--remove">
                                                                        <i class="fa fa-trash"></i>
                                                                    </span>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <?
                                                                echo CustomHelper::generateInput($form, $container,
                                                                    "[$container->primaryKey]nr", [
                                                                        'class' => 'name',
                                                                        'tag' => false,
                                                                    ], '{input}', [
                                                                        'class' => '',
                                                                    ], $disabledFields);
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                $disabledSoc = $action == 'view' ? true : false;
                                                                echo $form->field($container,
                                                                    "[$container->primaryKey]soc", [
                                                                        'template' => '{input}',
                                                                        'options' => [
                                                                            'tag' => false,
                                                                        ]
                                                                    ])->checkbox([
                                                                    'label' => false,
                                                                    'disabled' => $disabledSoc,
                                                                ])
                                                                ?>
                                                                <script>
                                                                    //container_IDs[<?=$certificateContainersType->id_container_type?>].push('<?= $container->nr?>');
                                                                </script>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    ?>
                                                </table>
                                                <div class="modal-wrapper multiple-line--wrapper">
                                                    <div class="multiple-line--modal">
                                                        <?php
                                                        if ($action != 'view') {
                                                            ?>
                                                            <div class="modal-header">
                                                                <p>
                                                                    <?= Lx::t('frontend', 'Add Multiple Containers'); ?>
                                                                </p>
                                                                <?= Lx::t('frontend', 'one per line'); ?>
                                                            </div>
                                                            <div class="modal-body">
                                                                <textarea rows="5"></textarea>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button class="modal-close--btn" type="button"><?= Lx::t('frontend',
                                                                        'Close'); ?></button>
                                                                <button class="modal-submit--btn" type="button"><?= Lx::t('frontend',
                                                                        'Save'); ?></button>
                                                            </div>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="no-results">
                                                <p><?= Lx::t('frontend', "There are no ID's to show"); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-8 certificate-box--column vehicles">
                                    <div class="subcontainer">
                                        <div class="container-group--head">
                                            <div class="subcontainer-head--title">
                                                <p><?= Lx::t('frontend', 'Vehicles'); ?></p>
                                            </div>
                                        </div>
                                        <div class="subcontainer-body">
                                            <div class="row">
                                                <div class="col-xs-12 col-sm-4 certificate-box--column">
                                                    <div class="input-wrapper">
                                                        
                                                        <label for="container-vehicles"><?= Lx::t('frontend',
                                                                'Contains Vehicles'); ?></label>
                                                        <?php echo CustomHelper::generateDropdownlist($form,
                                                            $certificateContainersType, "vehicles_contains",
                                                            ['class' => 'container-vehicles'],
                                                            $certificateContainersType::CONTAINS_VEHICLES); ?>
                                                        
                                                        <!--<select class="container-vehicles" type="text">
                                                            <option value="no"><?/*= Yii::t('frontend', 'No'); */ ?></option>
                                                            <option value="yes"><?/*= Yii::t('frontend',
                                                                    'Yes'); */ ?></option>
                                                        </select>-->
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row vehicles-form <?= $certificateContainersType->vehicles_contains == 1 ? 'open' : ''; ?>">
                                                <div class="col-xs-12 col-sm-6 certificate-box--column">
                                                    <div class="container-body--group">
                                                        <div class="container-group--head">
                                                            <p><?= Lx::t('frontend', 'New Vehicles'); ?></p>
                                                        </div>
                                                        <div class="container-group--body">
                                                            <div class="row">
                                                                <div class="col-xs-12 col-sm-12 certificate-box--column">
                                                                    <?php
                                                                    echo $form->field($certificateContainersType,
                                                                        "[$certificateContainersType->primaryKey]vehicles_new",
                                                                        [
                                                                            'template' => '{label}{input}',
                                                                            'options' => [
                                                                                'class' => 'input-wrapper',
                                                                            ],
                                                                            'labelOptions' => [
                                                                                'class' => '',
                                                                            ]
                                                                        ])->textarea([
                                                                        'rows' => 3,
                                                                    ]);
                                                                    ?>
                                                                </div>
                                                                <div class="col-xs-12 col-sm-4 certificate-box--column">
                                                                    <div class="input-wrapper">
                                                                        <label for="new-id--count">
                                                                            <?= Lx::t('frontend', 'Count'); ?>
                                                                        </label>
                                                                        <span class="new-id--count" type="text">12</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-sm-6 certificate-box--column">
                                                    <div class="container-body--group">
                                                        <div class="container-group--head">
                                                            <p><?= Lx::t('frontend', 'Used Vehicles'); ?></p>
                                                        </div>
                                                        <div class="container-group--body">
                                                            <div class="row">
                                                                <div class="col-xs-12 col-sm-12 certificate-box--column">
                                                                    <?php
                                                                    echo $form->field($certificateContainersType,
                                                                        "[$certificateContainersType->primaryKey]vehicles_used",
                                                                        [
                                                                            'template' => '{label}{input}',
                                                                            'options' => [
                                                                                'class' => 'input-wrapper',
                                                                            ],
                                                                            'labelOptions' => [
                                                                                'class' => '',
                                                                            ]
                                                                        ])->textarea([
                                                                        'rows' => 3,
                                                                    ]);
                                                                    ?>
                                                                </div>
                                                                <div class="col-xs-12 col-sm-4 certificate-box--column">
                                                                    <div class="input-wrapper">
                                                                        <label for="used-id--count">
                                                                            <?= Lx::t('frontend', 'Count'); ?>
                                                                        </label>
                                                                        <span class="used-id--count" type="text">33</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                
                                </div>
                                <div class="col-xs-12 certificate-box--column">
                                    <div class="container-body--group">
                                        <div class="container-group--head">
                                            <p><?= Lx::t('frontend', 'Measurements'); ?></p>
                                        </div>
                                        <div class="container-group--body">
                                            <div class="row">
                                                <div class="col-xs-12 col-sm-6 certificate-box--column">
                                                    <?
                                                    echo CustomHelper::generateInput($form, $certificateContainersType,
                                                        "[$certificateContainersType->primaryKey]packages_nr", [
                                                            'class' => 'input-wrapper packages-nr',
                                                        ], '{label}{input}', [
                                                            'class' => '',
                                                        ], $disabledFields);
                                                    ?>
                                                </div>
                                                <div class="col-xs-12 col-sm-6 certificate-box--column">
                                                    <?
                                                    echo CustomHelper::generateInput($form, $certificateContainersType,
                                                        "[$certificateContainersType->primaryKey]volume", [
                                                            'class' => 'input-wrapper volume',
                                                        ], '{label}{input}', [
                                                            'class' => '',
                                                        ], $disabledFields);
                                                    ?>
                                                </div>
                                                <div class="col-xs-12 col-sm-6 certificate-box--column">
                                                    <div class="input-wrapper">
                                                        <label for="container-wheight"><?= Lx::t('frontend',
                                                                'Weight'); ?></label>
                                                        <div class="certificate-box--flex">
                                                            <?php
                                                            $weightContainer = $certificateContainersType->weight * ($certificateContainersType->weight_unit == 1 ? 1000 : 1);
                                                            $weightContainers += $weightContainer;
                                                            
                                                            echo $form->field($certificateContainersType,
                                                                "[$certificateContainersType->primaryKey]weight_unit", [
                                                                    'template' => '{input}',
                                                                    'options' => [
                                                                        'tag' => false,
                                                                    ]
                                                                ])->dropDownList([
                                                                '0' => 'K',
                                                                '1' => 'T',
                                                            ], [
                                                                'label' => false,
                                                                'class' => 'container-weight--unit'
                                                            ]);
                                                            
                                                            echo CustomHelper::generateInput($form,
                                                                $certificateContainersType,
                                                                "[$certificateContainersType->primaryKey]weight", [
                                                                    'class' => 'name',
                                                                    'tag' => false,
                                                                ], '{input}', [
                                                                    'class' => 'container-weight',
                                                                ], $disabledFields);
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-sm-6 certificate-box--column">
                                                    <?
                                                    echo CustomHelper::generateInput($form, $certificateContainersType,
                                                        "[$certificateContainersType->primaryKey]freight", [
                                                            'class' => 'input-wrapper freight',
                                                        ], '{label}{input}', [
                                                            'class' => '',
                                                        ], $disabledFields);
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 certificate-box--column btns-wrapper">
                                    <?php
                                    if ($action != 'view') {
                                        ?>
                                        <button type="button" class="container-remove--btn flex-btn">
                                            <i class="fa fa-times"></i>
                                            <span>Remove</span>
                                        </button>
                                        <div class="submit-wrapper">
                                            <?php
                                            echo Html::submitButton('<i class="fa fa-save"></i><span>Submit</span>', [
                                                'class' => 'container-submit--btn flex-btn',
                                            ]);
                                            ?>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
            <div class="totals-wrapper container-burden">
                <div class="totals-item">
                    <p class="totals-label">Containers</p>
                    <p class="totals-value">12</p>
                    <p class="totals-label">Weigth</p>
                    <p class="totals-value"><?= $weightContainers ?> K</p>
                </div>
            </div>
            
            <?php
            if ($action != 'view') {
                ?>
                <div class="new-container--wrapper margin-top-20">
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                            <i class="fa fa-plus"></i>
                            <span>Add Stowage</span>
                            <i class="fa fa-caret-down"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <?php
                            foreach ($addContainerTypes as $key => $value) {
                                ?>
                                <li>
                                    <a href="#" data-container-type-id="<?= $key ?>" data-container-type-name="<?= $value ?>"><?= $value ?></a>
                                </li>
                                <?
                            }
                            ?>
                        </ul>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
	<?php
	$buttonHSProduct = '';
	if($action == 'view') {
	    $hsProduct = Lx::t('frontend','Export HS Products in Spreadsheet');
	    $buttonHSProduct = '<p><div class="dt-buttons">
            <a href="#" onclick="window.open(\'/certificates/export-hs?id='.$certificate->id_certificate.'\',\'_blank\')" style="color: #FFF;">
	            <small>'.$hsProduct.'</small>
            </a>
        </div></p>';
	}
	?>

    <div class="certificate-box--item margin-top-20 tariffcodes">
        <div class="certificate-box--head">
            <p><?= Lx::t('frontend', 'Products'); ?></p>
	        <?=$buttonHSProduct;?>
        </div>
	    
        <div class="certificate-box--body">
            <table class="product-table" width="100%">
                <thead>
                <tr class="product-table--head">
                    <th></th>
                    <th class="select-all--wrapper">
                        <input type="checkbox" class="select-all">
                    </th>
                    <th><?= Lx::t('frontend', 'HS Code'); ?></th>
                    <th class="description--wrapper"><?= Lx::t('frontend', 'Description'); ?></th>
                    <th class="country--wrapper"><?= Lx::t('frontend', 'Country of Origin'); ?></th>
                    <th><?= Lx::t('frontend', 'Quantity'); ?></th>
                    <th><?= Lx::t('frontend', 'Value'); ?> €</th>
                    <th><?= Lx::t('frontend', 'Weight'); ?> K</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $c = $tariffQty = $tariffValue = $tariffWeight = 0;
                foreach ($tariffcodes as $key => $tariffcode) {
                    $c++;
                    $tariffQty += $tariffcode->qty;
                    $tariffValue += $tariffcode->value;
                    $tariffWeight += $tariffcode->weight;
                    $weightTariffcodes += $tariffcode->weight;
                    ?>
                    <tr>
                        
                        <td></td>
                        <td class="select-row--wrapper"></td>
                        <td>
                            <span class='dataTables-value'><?= $tariffcode->code ?></span>
                            <input type="text" name="CertificateTariffcodes[<?= $tariffcode->primaryKey ?>][code]" class="tariffcode" value="<?= $tariffcode->code ?>" <?= $action == 'view' ? 'disabled' : ''; ?>>
                            
                            <input type="hidden" name="CertificateTariffcodes[<?= $tariffcode->primaryKey ?>][id_tariffcodes_class]" class="tariff-code-class" value="<?= $tariffcode->id_tariffcodes_class ?>" <?= $action == 'view' ? 'disabled' : ''; ?>>
                            <input type="hidden" name="CertificateTariffcodes[<?= $tariffcode->primaryKey ?>][id_tariffcodes_category]" class="tariff-code-category" value="<?= $tariffcode->id_tariffcodes_category ?>" <?= $action == 'view' ? 'disabled' : ''; ?>>
                            <input type="hidden" name="CertificateTariffcodes[<?= $tariffcode->primaryKey ?>][id_tariffcodes_product]" class="tariff-code-product" value="<?= $tariffcode->id_tariffcodes_product ?>" <?= $action == 'view' ? 'disabled' : ''; ?>>
                        </td>
                        <td>
                            <span class='dataTables-value'><?= $tariffcode->description ?></span>
                            <input type="text" name="CertificateTariffcodes[<?= $tariffcode->primaryKey ?>][description]" class="" value="<?= $tariffcode->description ?>" <?= $action == 'view' ? 'disabled' : ''; ?>>
                        </td>
                        <td>
                            <span class='dataTables-value'><?= $tariffcode->id_country ?></span>
                            <?= CustomHelper::generateCountryDropdownlist($form, $tariffcode,
                                "[{$tariffcode->primaryKey}]id_country", [], Yii::t('frontend', 'Country of Loading'),
                                $disabledFields); ?>
                        </td>
                        <td>
                            <span class='dataTables-value'><?= $tariffcode->qty ?></span>
                            <input type="text" name="CertificateTariffcodes[<?= $tariffcode->primaryKey ?>][qty]" class="tariff-qty" value="<?= $tariffcode->qty ?>" <?= $action == 'view' ? 'disabled' : ''; ?>>
                        </td>
                        <td>
                            <span class='dataTables-value'><?= $tariffcode->value ?></span>
                            <input type="text" name="CertificateTariffcodes[<?= $tariffcode->primaryKey ?>][value]" class="tariff-value" value="<?= $tariffcode->value ?>" <?= $action == 'view' ? 'disabled' : ''; ?>>
                        </td>
                        <td>
                            <span class='dataTables-value'><?= $tariffcode->weight ?></span>
                            <input type="text" name="CertificateTariffcodes[<?= $tariffcode->primaryKey ?>][weight]" class="tariff-weight" value="<?= $tariffcode->weight ?>">
                        </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
            <div class="modal-wrapper multiple-line--wrapper">
                <div class="multiple-line--modal">
                    <?php
                    if ($action != 'view') {
                        ?>
                        <div class="modal-header">
                            <p>
                                <?= Lx::t('frontend', 'Add Multiple HS Codes'); ?>
                            </p>
                            <?= Lx::t('frontend', 'one per line'); ?>
                        </div>
                        <div class="modal-body">
                            <textarea rows="5"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button class="modaltariffcodes-close--btn" type="button"><?= Lx::t('frontend',
                                    'Close'); ?></button>
                            <button class="modaltariffcodes-submit--btn" type="button"><?= Lx::t('frontend',
                                    'Save'); ?></button>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <div class="totals-wrapper">
                <div class="totals-item">
                    <p class="totals-label">Products total</p>
                    <p class="totals-value products-total">0</p>
                </div>
                <div class="totals-item">
                    <p class="totals-label">QUANTITY</p>
                    <p class="totals-value quantity-total">0.000</p>
                </div>
                <div class="totals-item">
                    <p class="totals-label">value total</p>
                    <p class="totals-value value-total">0.000 €</p>
                </div>
                
                <div class="totals-item">
                    <p class="totals-label">WEIGHT TOTAL</p>
                    <p class="totals-value weights-total"><?= $weightTariffcodes ?> K</p>
                </div>
                <div class="totals-item">
                    <p class="totals-label">WEIGHTS DIFFERENCE</p>
                    <p class="totals-value totals-value--weightdiff"><?= ($weightContainers - $weightTariffcodes) ?> K</p>
                </div>
            </div>
        </div>
    </div>

<?php
if ($action != 'view') {
    ?>
    <div class="clearfix">
        <div class="col-md-12 margin-top-20">
            <?php
            echo Html::submitButton('' . ($certificate->isNewRecord ? Lx::t('cruds', 'Create') : Lx::t('cruds',
                    'Save Certificate')), [
                'id' => 'save-' . $certificate->formName(),
                'class' => 'btn certificates-btn save pull-right',
            ]);
            ?>
        </div>
    </div>
    <?php
}
?>
<?php
ActiveForm::end();
?>

<?php
$jsString = "";
$certificateContainersTypesJs = $certificateContainersJs = [];
foreach ($containersTypes as $certificateContainersType) {
    if (!empty($jsString)) {
        $jsString .= ","; // that final comma
    }
    $certificateContainersTypesJs[] = $certificateContainersType->id_container_type;
    $certificateContainersJs = [];
    foreach ($certificateContainersType->certificateContainers as $container) {
        $certificateContainersJs[] = $container->nr;
    }
    
    $jsString .= $certificateContainersType->id_container_type . ': [\'' . implode($certificateContainersJs,
            '\',\'') . '\']';
    
}

$certificateContainersTypesJsString = implode($certificateContainersTypesJs, '":[],"');
$certificateContainersJsString = implode($certificateContainersJs, '","');

foreach ($containersTypes as $certificateContainersType) {
    $certificateContainersTypesJs[] = $certificateContainersType->id_container_type;
    foreach ($certificateContainersType->certificateContainers as $container) {
        $certificateContainersJs[] = $container->nr;
    }
}

$indexContainer = $maxCertificateContainers;
$indexContainerType = $maxCertificateContainersTypes;

$containerBurden = $action == 'create' || count($containersTypes) == 0 ? "$('.container-burden').hide();": '';

$newForm = <<<JS
    
    let container_IDs = { $jsString };
    let indexContainer = $indexContainer;
    let indexContainerType = $indexContainerType;
    
$(document).ready(function () {
	
	$containerBurden
	
	
    /* Datepicker */
    $('.datepicker input').datetimepicker({
        format: 'L'
    });
    /* End of Datepicker */
    
    $('#certificate-tx-id').on('change', function() {
        if ( $(this).val() == 'no' ) {
            $('#certificate-tx-name2').closest('.certificate-box--column').show();
            $(this).closest('.row').next('.row').hide();
        } else {
            $('#certificate-tx-name2').closest('.certificate-box--column').hide();
            $(this).closest('.row').next('.row').show();
        }
    })
    
    $('#certificate-bank-option').on('change', function() {
        if ( $(this).val() == 'yes' ) {
            $('#certificate-bank').closest('.certificate-box--column').show();
        } else {
            $('#certificate-bank').closest('.certificate-box--column').hide();
        }
    })
    
    /* open and close container */
    $('body').on('click', '.container-head', function(){
        let element = $(this).closest('.container-wrapper');
        
        if ( element.hasClass('open') ) {
            element.removeClass('open');
        } else {
            element.addClass('open');
        }
    })
    /* end of open and close container */
    
    /* open/close vehicles form */
    $('.container-list').on('change', '.container-vehicles', function() {
        if ( $(this).val() == '1' ) {
            $(this).closest('.vehicles').find('.vehicles-form').addClass('open');
        } else {
            $(this).closest('.vehicles').find('.vehicles-form').removeClass('open');
        }
    })
    /* end of open/close vehicles form */
    
    /* table search */
    
    let suggestionArray = new Array();
    var substringMatcher = function(strs) {
      return function findMatches(q, cb) {
        var matches, substringRegex;
        // an array that will be populated with substring matches
        matches = [];
    
        // regex used to determine if a string contains the substring `q`
        substrRegex = new RegExp(q, 'i');
    
        // iterate through the pool of strings and for any string that
        // contains the substring `q`, add it to the `matches` array
        $.each(strs, function(i, str) {
          if (substrRegex.test(str)) {
            matches.push(str);
          }
        });
        
        clearArray(suggestionArray);
        suggestionArray.push(matches);
        
        cb(matches);
      };
    };
    
    $('.container--table-search').each(function(){
        var input = $(this);
        var element_source = input.closest('.container-wrapper').attr("container-type-id");
        
        input.typeahead(
            {
              hint: true,
              highlight: true,
              minLength: 1,
            },
            {
              name: 'IDs',
              source: substringMatcher(container_IDs[element_source]),
            }
        );
        
        input.bind('typeahead:render', function(ev, suggestion) {
            compareElements(input, suggestionArray[0]);
        });
        
        input.bind('typeahead:select', function(ev, suggestion) {
            input.closest('.container-body--group').find('.container-table tr:not(.container-table--head) td input[type="text"]').each(function() {
                let element = $(this);
                let lineText = element.val();
            
                if ( suggestion != lineText ) {
                    element.closest('tr').hide();
                } else {
                    element.closest('tr').show();
                }
            });
        });
        
        input.on('input', function() {
            if ( $(this).val() == '' ) {
                input.closest('.container-body--group').find('.container-table tr').show();
                $(this).closest('.container-wrapper').find('.no-results').removeClass('show');
                $(this).closest('.container-wrapper').find('.container-table').show();
            }
        });
    });
    
    function compareElements(input, suggestionArray) {
        let wrapper = input.closest('.container-wrapper');
        
        if ( suggestionArray.length > 0 ) {
            if ( wrapper.find('.container-group--body .no-results').hasClass('show') ) {
                wrapper.find('.container-group--body .no-results').removeClass('show');
                wrapper.find('.container-table').show();
            }
            
            wrapper.find('.container-table tr:not(.container-table--head) td input[type="text"]').each(function() {
                let element = $(this);
                let lineText = element.val();
                
                if ( !suggestionArray.includes(lineText) ) {
                    element.closest('tr').hide();
                } else {
                    element.closest('tr').show();
                }
            })
        } else {
            if ( input.val() != '' ) {
                wrapper.find('.container-table').hide();
                wrapper.find('.container-group--body .no-results').addClass('show');
            } else {
                wrapper.find('.container-table').show();
                wrapper.find('.container-group--body .no-results').removeClass('show');
            }
            
        }
    }
    
    function clearArray(array) {
      while (array.length) {
        array.pop();
      }
    }
    /* end of table search */
    
    /* Remove line from table */
    $('body').on('click', '.container-table--remove', function() {
        let input_value = $(this).closest('tr').find('input[type="text"]').val();
        let array_position = $(this).closest('.container-wrapper').attr("container-type-id");
        for (var i=container_IDs[array_position].length-1; i>=0; i--) {
            if (container_IDs[array_position][i] === input_value) {
                container_IDs[array_position].splice(i, 1);
                break;
            }
        }
        
        $(this).closest('tr').remove();
    })
    /* End of remove line from table */
    
    /* Add one line to table */
    $('body').on('click', '.container-table--add', function(e) {
        e.preventDefault();
        
        let containerClone = $.parseHTML($(".hidden .container").html());
        let containerBodyGroup = $(this).closest('.container-body--group');
        let containerType = containerBodyGroup.find('input.table-container-type').val();
        let containerTable = containerBodyGroup.find('.container-table');
        
        containerTable.append(containerClone);
        $(this).closest('.container-body--group').find('.container-table tbody:last tr:last-child').html(function(i, v) {
            return v.replace(/index|INDEX/g, indexContainer++);
        })
        
        
        $(this).closest('.container-body--group').find('.container-table tbody:last tr:last-child input[type="text"]').focus();
        $(this).closest('.container-body--group').find('.container-table .hidden-container-type').val(containerType);
       
    })
    
    $('body').on('focusout', '.container-table tr:last-child input[type="text"]', function() {
        let containerType = $(this).closest('.container-wrapper').attr('container-type-id');
        
        if ( $(this).val() == '' ) {
            $(this).closest('tr').remove();
        } else {
            container_IDs[containerType].push( $(this).val() );
        }
    })
    /* End of add one line to table */
    
    /* add multiple lines to table */
    $('body').on('click', '.container-table--addGroup', function(e) {
        e.preventDefault();
        $(this).closest('.container-body--group').find('.multiple-line--wrapper').addClass('open');
    })
    
    $('body').on('click', '.modal-close--btn', function(e) {
        e.preventDefault();
        $(this).closest('.multiple-line--wrapper').removeClass('open');
    })
    
    $(document).mouseup(function (e) {
        if (!$('.multiple-line--modal').is(e.target) && $('.multiple-line--modal').has(e.target).length === 0) {
            $('.multiple-line--wrapper.open .modal-close--btn').trigger('click');
        }
    });
    
    $('body').on('click', '.modal-submit--btn', function(e) {
        e.preventDefault();
        let containerType = $(this).closest('.container-wrapper').attr('container-type-id');
        let value = $(this).closest('.multiple-line--modal').find('textarea').val();
        let lines = value.split('\\n');
        for (var i = 0; i < lines.length; i++) {
            if(lines[i] != "" && lines[i] != undefined){
                let containerValues = lines[i].split(",");
                let containerName;
                let containerSoc;
                
                if( containerValues[0] == undefined ) {
                    containerValues[0] = '';
                }
                if( containerValues[1] == undefined ) {
                    containerValues[1] = 0;
                }
                containerName = containerValues[0];
                containerSoc = containerValues[1];
                
                let containerClone = $.parseHTML($(".hidden .container").html());
                let containerBodyGroup = $(this).closest('.container-body--group');
                let containerType = containerBodyGroup.find('input.table-container-type').val();
                let containerTable = containerBodyGroup.find('.container-table');
                
                $(this).closest('.container-body--group').find('.container-table').append(containerClone);
                $(this).closest('.container-body--group').find('.container-table tbody:last tr:last-child').html(function(i, v) {
                    return v.replace(/index|INDEX/g, indexContainer++);
                })
                
                $(this).closest('.container-body--group').find('.container-table tbody:last tr:last-child input[type="text"]').val(containerName);
                if(containerSoc == 1){
                    $(this).closest('.container-body--group').find('.container-table tbody:last tr:last-child input[type="checkbox"]').click();
                }
                $(this).closest('.container-body--group').find('.container-table .hidden-container-type').val(containerType);
                container_IDs[containerType].push( lines[i] );
            }
        }
        
        $(this).closest('.multiple-line--modal').find('textarea').val('');
        $(this).closest('.modal-footer').find('.modal-close--btn').trigger('click');
    })
    /* end of add multiple lines to table */
    
    /* Remove Container */
    $('body').on('click', '.certificate-box--column.btns-wrapper button.container-remove--btn', function() {
        let container = $(this).closest('.container-wrapper');
        let containerType = container.attr('container-type-id');
        delete container_IDs[containerType];
        container.remove();
    })
    /* End of Remove Container */
    
    /* ADD CONTAINER */
    $('.new-container--wrapper .dropdown-menu a').on('click', function(e) {
    	e.preventDefault();
        let container_type = $(this).data('container-type-id');
        let container_name = $(this).data('container-type-name');
        let newContainer = $(".hidden .containerType").clone(true);
        
        $('.container-burden').show();
        
        $('.container-list').append(newContainer);
        $('.container-list>div:last').html(function(i, v) {
            return v.replace(/index|INDEX/g, indexContainerType++);
        })
        $('.container-list>div:last').html(function(i, v) {
            return v.replace(/{CONTAINER-TYPE-ID}/g, container_type);
        })
        $('.container-list>div:last').html(function(i, v) {
            return v.replace(/{CONTAINER-TYPE-NAME}/g, container_name);
        })
        $('.container-list>div:last').attr("container-type-id", container_type);
        
        
        $('.container-list>div:last').find('.hidden-container-type').val(container_type);
        container_IDs[container_type] = [];
    })
    /* END OF ADD CONTAINER */
    
    /**
    $(":input").keyup(function() {
    	var form = $('#new-certificate--form');
    	disableSave();
    	if(form.find('.has-error').length === 0){
    		alert('aqui');
    		disableSave(false);
    	}
    });
    */
    
});
JS;

$this->registerJS($newForm);

$buttonPrintHSProductList = in_array(Yii::$app->user->identity->type,[User::TYPE_MANAGER,User::TYPE_MAKER]) && $action == 'update' ? "
{
    className: 'datatable-btn add-row $datatablesHiddenClass',
    text: '".Lx::t('frontend','Export HS Products in Spreadsheet')."',
    action: function () {
        window.open('export-hs?id=$certificate->id_certificate','_blank');
    }
},
" : '';

$certificateDuFile = $newForm = <<<JS

var formatMoney = new Intl.NumberFormat('an-AN', {
  style: 'currency',
  currency: 'EUR'
});
var formatNumbers = new Intl.NumberFormat('an-AN');

let indexDU = $indexDu;
$(document).ready(function () {
	
	/* Products - Sums */
	let productsTotal = $('.products-total');
	let quantity = $('.quantity-total');
	let valueTotal = $('.value-total');
	let weightsTotal = $('.weights-total');
	let weightsDifference = $('.weights-difference');
	
	productsTotal.html(formatNumbers.format($c));
	quantity.html(formatNumbers.format($tariffQty));
	valueTotal.html(formatMoney.format($tariffValue));
	weightsTotal.html(formatNumbers.format($tariffWeight)+'  K');
	
	$('.tariff-qty').keyup(function() {
		let quantity = 0;
		
		$.each($('.tariff-qty'), function(index, element) {
			let value = parseFloat($(element).val());
			let total = !isNaN(value)? value:0;
			quantity += total;
			
		});
		
		$('.quantity-total').html(quantity);
	});
	$('.tariff-value').keyup(function() {
		let quantity = 0;
		
		$.each($('.tariff-value'), function(index, element) {
			let value = parseFloat($(element).val());
			let total = !isNaN(value)? value:0;
			quantity += total;
		});
		
		$('.value-total').html(quantity+' €');
	});
	$('.tariff-weight').keyup(function() {
		let quantity = 0;
		
		$.each($('.tariff-weight'), function(index, element) {
			let value = parseFloat($(element).val());
			let total = !isNaN(value)? value:0;
			quantity += total;
		});
		
		$('.weights-total').html(quantity+' k');
	});
	/* End of Products - Sums */
	
	/* Products - Datatables */
    /*let datatable = $('.product-table').DataTable();
    
    $('body').on( 'click', '.product-table .product-table--remove', function () {
    	let totalProduct = $('.products-total').html();
		totalProduct = !isNaN(totalProduct)? parseFloat(totalProduct):0;
		
		//Total Product
		totalProduct--;
		$('.products-total').html(totalProduct);
    	
        datatable.row( $(this).parents('tr') ).remove().draw();
    });*/
    /* End of Products - Datatables */
    
    /* Products - Datatables */
    
    
    let countIndex = tariffcodesINDEX;
    
    let datatable = $('.product-table').DataTable({
        columnDefs:[{
            targets:[0],
            render : function(){
                return '<span class="product-table--remove $datatablesHiddenClass"><i class="fa fa-trash"></i></span>';
            }
        },
        {
            targets:[1],
            render : function(){
                return '<input type="checkbox" class="select-row $datatablesHiddenClass">';
            }
        },
        {
            targets:[3,4,5,6,7]
            
        }],
        dom: 'Bfrtip',
        buttons: [
        	$buttonPrintHSProductList
            {
                className: 'datatable-btn add-row tariffcodes--addGroup $datatablesHiddenClass',
                text: 'Add Multiple Products',
                /*
                action: function ( e, dt, node, config ) {
                    datatable.row.add([
	                    '',
	                    '',
	                    createInput(countIndex,'code','tariffcode',true),
	                    createInput(countIndex,'description',''),
	                    createCountrySelect(null,countIndex),
	                    createInput(countIndex,'qty','tariff-qty'),
	                    createInput(countIndex,'value','tariff-value'),
	                    createInput(countIndex,'weight','tariff-weight')]).draw();
                    countIndex++;
                    $('.products-total').html(parseInt(productsTotal.html())+1);
                }
                */
            },
            {
                className: 'datatable-btn add-row $datatablesHiddenClass',
                text: 'Add Product',
                action: function ( e, dt, node, config ) {
                    datatable.row.add([
	                    '',
	                    '',
	                    createInput(countIndex,'code','tariffcode',true),
	                    createInput(countIndex,'description',''),
	                    createCountrySelect(null,countIndex),
	                    createInput(countIndex,'qty','tariff-qty'),
	                    createInput(countIndex,'value','tariff-value'),
	                    createInput(countIndex,'weight','tariff-weight')]).draw();
                    countIndex++;
                    $('.products-total').html(parseInt(productsTotal.html())+1);
                }
            },
            {
                className: 'datatable-btn remove-rows $datatablesHiddenClass',
                text: 'Remove',
                action: function ( e, dt, node, config ) {
                    $('.product-table tr:not(.product-table--head)').each(function() {
                        if ( $(this).find('.select-row').is(':checked') ) {
                            datatable.row( $(this) ).remove().draw();
                            $('.select-all').prop('checked', false)
                            $('.products-total').html(parseInt(productsTotal.html())-1);
                        }
                    });
                }
            }
        ]
    });
    
    
    
    
    /* add multiple lines to table */
    $('body').on('click', '.tariffcodes--addGroup', function(e) {
        e.preventDefault();
        $(this).closest('.tariffcodes').find('.multiple-line--wrapper').addClass('open');
    })
    
    $('body').on('click', '.modaltariffcodes-close--btn', function(e) {
        e.preventDefault();
        $(this).closest('.multiple-line--wrapper').removeClass('open');
    })
    
    $(document).mouseup(function (e) {
        if (!$('.multiple-line--modal').is(e.target) && $('.multiple-line--modal').has(e.target).length === 0) {
            $('.multiple-line--wrapper.open .modaltariffcodes-close--btn').trigger('click');
        }
    });
    
    $('body').on('click', '.modaltariffcodes-submit--btn', function(e) {
        let value = $(this).closest('.multiple-line--modal').find('textarea').val();
        let lines = value.split('\\n');
        for (var i = 0; i < lines.length; i++) {
            if(lines[i] != "" && lines[i] != undefined){
                let lineArray = lines[i].split(",");
                
                let hscode;
                let hsdesc;
                let hscc2;
                let hsqty;
                let hsvalue;
                let hsweight;
                
                if( lineArray[0] == undefined ) {
                    lineArray[0] = '';
                }
                if( lineArray[1] == undefined ) {
                    lineArray[1] = 0;
                }
                if( lineArray[2] == undefined ) {
                    lineArray[2] = 0;
                }
                if( lineArray[3] == undefined ) {
                    lineArray[3] = 0;
                }
                if( lineArray[4] == undefined ) {
                    lineArray[4] = 0;
                }
                if( lineArray[5] == undefined ) {
                    lineArray[5] = 0;
                }
                
                hscode = lineArray[0];
                hsdesc = lineArray[1];
                hscc2 = lineArray[2];
                hsqty = lineArray[3];
                hsvalue = lineArray[4];
                hsweight = lineArray[5];
                
                datatable.row.add([
	                    '',
	                    '',
	                    createInput(countIndex,'code','tariffcode',true,hscode),
	                    createInput(countIndex,'description','',false,hsdesc),
	                    createCountrySelect(null,countIndex, hscc2),
	                    createInput(countIndex,'qty','tariff-qty',false,hsqty),
	                    createInput(countIndex,'value','tariff-value',false,hsvalue),
	                    createInput(countIndex,'weight','tariff-weight',false,hsweight)
	                    ]).draw();
                    countIndex++;
                    
                $('.products-total').html(parseInt(productsTotal.html())+1);
                $('.product-table input.tariffcode:last').trigger("change");
            }
        }
        
        
        $(this).closest('.multiple-line--modal').find('textarea').val('');
        $(this).closest('.modal-footer').find('.modaltariffcodes-close--btn').trigger('click');
    });
    
    // the function - create a input
    function createInput(index,name,classess,hidden = false, value=null){
    	if(hidden){
    		hidden = "<input type='hidden' name='CertificateTariffcodes["+ index +"][id_tariffcodes_class]' class='tariff-code-class' > "+
            "<input type='hidden' name='CertificateTariffcodes["+ index +"][id_tariffcodes_category]' class='tariff-code-category'>"+
            "<input type='hidden' name='CertificateTariffcodes["+ index +"][id_tariffcodes_product]' class='tariff-code-product'>"+
            "<input type='hidden' name='CertificateTariffcodes["+ index +"][code_tariffcodes_class]' class='tariff-code-class'>"+
            "<input type='hidden' name='CertificateTariffcodes["+ index +"][code_tariffcodes_category]' class='tariff-code-category'>"+
            "<input type='hidden' name='CertificateTariffcodes["+ index +"][code_tariffcodes_product]' class='tariff-code-product'>"+
            "<input type='hidden' name='CertificateTariffcodes["+ index +"][desc_tariffcodes_class]' class='tariff-description-class'>"+
            "<input type='hidden' name='CertificateTariffcodes["+ index +"][desc_tariffcodes_category]' class='tariff-description-category'>"+
            "<input type='hidden' name='CertificateTariffcodes["+ index +"][desc_tariffcodes_product]' class='tariff-description-product'>";
    	}else{
    		hidden="";
    	}
    	
    	
    	valueString = "";
    	if(value != null){
    	    valueString = "value='"+ value +"'";
    	}
    	return hidden+"<input class='"+ classess +"' type='text' name='CertificateTariffcodes["+ index +"]["+ name +"]' "+valueString+">";
    }
    
    // the function creates a select box
    function createCountrySelect(data,index,value='PT'){
    	let countries = $countries;
        let span = $("<span class='dataTables-value'>"+ data +"</span>");
        let sel = "<select name='CertificateTariffcodes["+ index +"][id_country]'>" ;
        let selector = '';
        
        console.log(value);
        for ( var i = 0; i < countries.length; ++i ) {
            selector = value.toUpperCase() == countries[i].value? 'selected':'';//replace data to PT
            sel += "<option "+ selector +" value = '"+countries[i].value+"' >" + countries[i].text + "</option>";
        }
        sel += "</select>";
        
        
        let select = $(sel);
        if(value != null){
            select.val(value);
        }
        let element = span[0].outerHTML + select[0].outerHTML;
        return element;
    }
    
    $('body').on( 'click', '.product-table .product-table--remove', function () {
        datatable.row( $(this).parents('tr') ).remove().draw();
        $('.products-total').html(parseInt(productsTotal.html())-1);
    });
    
    function updateDatatable(element,value) {
    	
    	//get Tariffcode
    	if(element.hasClass('tariffcode')){
    		getTariffCode(element,true);
    	}
    	//get tariff-qty
    	if(element.hasClass('tariff-qty')){
    		let quantity = 0;
    		
    		$.each($('.tariff-qty'), function(index, element) {
				let value = parseFloat($(element).val());
				let total = !isNaN(value)? value:0;
				quantity += total;
				
			});
			
			$('.quantity-total').html(formatNumbers.format(quantity));
    	}
    	//get tariff-value
    	if(element.hasClass('tariff-value')){
    		let quantity = 0;
    		
    		$.each($('.tariff-value'), function(index, element) {
				let value = parseFloat($(element).val());
				let total = !isNaN(value)? value:0;
				quantity += total;
			});
			
			$('.value-total').html(formatNumbers.format(quantity)+' €');
    	}
    	//get tariff-weight
    	if(element.hasClass('tariff-weight')){
    		let quantity = 0;
    		
    		$.each($('.tariff-weight'), function(index, element) {
				let value = parseFloat($(element).val());
				let total = !isNaN(value)? value:0;
				quantity += total;
			});
    		
    		$('.weights-total').html(formatNumbers.format(quantity)+' k');
    	}
    	
        let datatableCell = element.closest('td');
        let cellindex = datatableCell.index();
        let rowIndex = element.closest('tr').index();
        let datatableRow = datatable.row( rowIndex );
        let data = datatableRow.data();
        
        data[cellindex] = value;
        //Block Update
        //datatable.row( rowIndex ).data( data ).draw();
    }
    
    $('body').on('change', 'table.dataTable td input:not([type="checkbox"]), table.dataTable td select', function() {
        let element = $(this);
        let value = element.val();
        
        element.closest('td').find('.dataTables-value').text(value);
        updateDatatable(element,value);
    });
    
    $('.select-all').on('click', function(e) {
        e.stopPropagation();
        if ( $(this).is(':checked') ) {
            $('.select-row').prop('checked', true);
        } else {
            $('.select-row').prop('checked', false);
        }
    });
    /* End of Products - Datatables */
    
    /* Datepicker */
    $('.datepicker input').datetimepicker({
        format: 'L'
    });
    /* End of Datepicker */
    
    /* Temporary DU Input */
    $('.certificate-box--body .add-file').on('click', function() {
        indexDU++;
        let name_element = $(".du-modal--container .input-wrapper-name").clone();
        let file_element = $(".du-modal--container .input-wrapper-file").clone();
        
        name_element.html(function(i, v) {
            return v.replace(/index|INDEX/g, indexDU);
        });
        
        file_element.html(function(i, v) {
            return v.replace(/index|INDEX/g, indexDU);
        });
        
        
        
        $('.file-modal--wrapper .file-modal--du .name').append(name_element);
        $('.file-modal--wrapper .file-modal--du .file').append(file_element);
        $('.file-modal--wrapper').addClass('modal-open add');
        return true;
        
        
        /*
        $('.file-modal--wrapper').addClass('modal-open add');
        
        fileName = $('.cloneDu .name').html();
        fileInput = $('.cloneDu .path').html();
        
        console.log(fileName);
        console.log(fileInput);
        
        $('.file-modal--wrapper .file-modal .certificate-box--column.name .input-field').append(fileName);
        $('.file-modal--wrapper .file-modal .certificate-box--column.file .input-field').append(fileInput);
        */
    });
    
    $('body').on('click', '.file-modal--wrapper .close-modal', function() {
        let modal = $(this).closest('.file-modal--wrapper');
        modal.removeClass('modal-open')
        if ( modal.hasClass('edit') ) {
            let index = modal.attr('data-index');
            $('.file-input-wrapper .file-item[data-index='+index+'] .hidden').append(modal.find(".name .input-wrapper"));
            $('.file-input-wrapper .file-item[data-index='+index+'] .hidden').append(modal.find(".file .input-wrapper"));
            $('.file-input-wrapper .file-item[data-index='+index+'] .hidden').append(modal.find(".link"));
        }
        
        if ( modal.hasClass('add') ) {
            //$(".du-modal--container .name").append(modal.find(".name .input-wrapper"));
            //$(".du-modal--container .file").append(modal.find(".file .input-wrapper"));
            
            modal.find(".name .input-wrapper").remove();
            modal.find(".file .input-wrapper").remove();
            
            //$(".du-modal--container .name").append($(".file-modal--wrapper .file-modal--du .name .input-wrapper"))
            //$('.du-modal--container .file').append($(".file-modal--wrapper .file-modal--du .file .input-wrapper"))
        }
        
        //modal.find(".link").remove();
        
        modal.removeClass('edit').removeClass('add');
        return true;
        
        
        /*
        $(".file-modal--wrapper .du-modal--new input.certificate-du--name").val('');
        $(".file-modal--wrapper .du-modal--new input.certificate-du--path").val('');
        if (modal.hasClass('add')) {
            $(".du-modal--new").append($(".file-modal--wrapper .du-modal--new"));
        } else if ( modal.hasClass('edit') ) {
            $(".du-modal--new").append($(".file-modal--wrapper .du-modal--new"));
            //$('.file-modal--submit').trigger('click');
        }
        
        modal.removeClass('modal-open').removeClass('edit').removeClass('add');
        //modal.find('.certificate-du--name').val('');
        modal.find('.certificate-box--column .input-wrapper').removeClass('has-error');
        */
    });
    
    $('body').on('click', '.certificate-box--body .file-item', function(e) {
        if ( e.target.className == "fa fa-close" ) {
            $(this).remove();
            
            let fields = $('.certificate-box--body .file-item');
            let count = 1;
            $.each(fields, function() {
                $(this).attr('data-index', count);
                count++;
            });
        } else {
            let index = $(this).attr('data-index');
            
            let file = $(this).find(".hidden .file");
            let name = $(this).find(".hidden .name");
            let link = $(this).find(".hidden .link");
            
            $('.file-modal--wrapper .file-modal--du .name').append(name);
            $('.file-modal--wrapper .file-modal--du .file').append(file);
            $('.file-modal--wrapper .file-modal--du .link-container').append(link);
            $('.file-modal--wrapper').addClass('modal-open edit').attr('data-index',index);
            
            /*
            let index = $(this).attr('data-index');
            console.log( index );
        
            let file_name = $(this).find('.file-name').text();
            let file_input = $(this).find('.certificate-admin-file');
            
            $('.file-modal--wrapper').addClass('modal-open edit').attr('data-index',index).find('.certificate-box--column.file .input-field').append(file_input);
            $('.certificate-du--name').val(file_name);
            */
            
        }
    });
    
    $('.file-modal--submit').on('click', function() {
        
        let file_name = $(this).closest('.file-modal').find('.certificate-du--name').val();
        let file_input = $(this).closest('.file-modal').find('.certificate-admin-file');
        
        if ( file_name != '' && file_name != undefined && file_input.val() != '' ) {
            if ( $('.file-modal--wrapper').hasClass('edit') ) {
                let index = $('.file-modal--wrapper').attr('data-index');
                let item = $('.certificate-box--body .file-item[data-index="'+ index +'"]');
                
                item.find('.file-name').text($(".file-modal--wrapper .name .input-wrapper input").val());
                //item.append(file_input);
                
                $('.file-input-wrapper .file-item[data-index='+index+'] .hidden').append($(".file-modal--wrapper .name .input-wrapper"));
                $('.file-input-wrapper .file-item[data-index='+index+'] .hidden').append($(".file-modal--wrapper .file .input-wrapper"));
                
                $('.file-modal--wrapper .close-modal').trigger('click');
            } else {
                let index = indexDU;
                
                let file_item = '<div class="file-item" data-index="'+ index +'">' +
                                    '<p class="file-name">'+ file_name +'</p>' +
                                    '<span><i class="fa fa-close" aria-hidden="true"></i></span>' +
                                    '<div class="hidden"></div>' +
                                '</div>';
                
                let modal = $(this).closest('.file-modal--wrapper');
                $('.file-input-wrapper').append(file_item);
                $('.file-input-wrapper .file-item[data-index='+index+'] .hidden').append($(".file-modal--wrapper .name .input-wrapper"));
                $('.file-input-wrapper .file-item[data-index='+index+'] .hidden').append($(".file-modal--wrapper .file .input-wrapper"));
                
                /*
                $('.file-input-wrapper .file-item[data-index='+index+'] .hidden').children().each(function (index) {
                    console.log(index);
                    
                    if( $(this).attr('name') != undefined){
                        $(this).attr('name', $(this).attr('name').replace('INDEX',indexDU));
                    }
                    if( $(this).attr('id') != undefined){
                        $(this).attr('id', $(this).attr('id').replace('index',indexDU));
                    }
                    if( $(this).attr('class') != undefined){
                        $(this).attr('class', $(this).attr('class').replace('index',indexDU));
                    }
                });
                */
                //indexDU++;
                
                /*
                let file_item = '<div class="file-item" data-index="'+ index +'">' +
                                    '<p class="file-name">'+ file_name +'</p>' +
                                    '<span><i class="fa fa-close" aria-hidden="true"></i></span>' +
                                '</div>';
                
                $('.file-input-wrapper').append(file_item);
                
                setTimeout( function() {
                    $('.file-item[data-index="'+ index +'"]').append(file_input)
                }, 300)
                */
                $('.file-modal--wrapper .close-modal').trigger('click');
            }
        } else {
            if ( file_name == '' ) {
                $('.file-modal--wrapper').find('.certificate-box--column.name .input-wrapper').addClass('has-error');
            } else if ( file_name != '' ) {
                $('.file-modal--wrapper').find('.certificate-box--column.name .input-wrapper').removeClass('has-error');
            }
            
            if ( file_input.val() == '' ) {
                $('.file-modal--wrapper').find('.certificate-box--column.file .input-wrapper').addClass('has-error');
            } else if ( file_input.val() != '' ) {
                $('.file-modal--wrapper').find('.certificate-box--column.file .input-wrapper').removeClass('has-error');
            }
        }
    });
    
    $(document).mouseup(function (e) {
        if (!$('.file-modal').is(e.target) && $('.file-modal').has(e.target).length === 0) {
            $('.file-modal--wrapper .close-modal').trigger('click');
        }
    });
    /* End of Temporary DU Input */
    
    $('#certificate-tx-id').on('change', function() {
        if ( $(this).val() == 'no' ) {
            $('#certificate-tx-name2').closest('.certificate-box--column').fadeIn();
            $(this).closest('.row').next('.row').slideUp();
        } else {
            $('#certificate-tx-name2').closest('.certificate-box--column').fadeOut();
            $(this).closest('.row').next('.row').slideDown();
        }
    })
    
    $('#certificate-bank-option').on('change', function() {
        if ( $(this).val() == 'yes' ) {
            $('#certificate-bank').closest('.certificate-box--column').slideDown();
        } else {
            $('#certificate-bank').closest('.certificate-box--column').slideUp();
        }
    })
});
JS;

$this->registerJS($newForm);

?>
    
    <div class="hidden form-clones--helpers">
        
        <!-- Start Clone TariffCode-->
        <table class="clone-tariffcode-row hidden" style="display: none;">
            <tr>
                <td>
                    <span class="product-table--remove">
                        <i class="fa fa-trash"></i>
                    </span>
                </td>
                <td>
                    <input type="text" name="CertificateTariffcodes[INDEX][code]" class="tariffcode" placeholder="<?= Yii::t('frontend',
                        'Type a tariff code') ?>">
                    
                    <input type="hidden" name="CertificateTariffcodes[INDEX][id_tariffcodes_class]" class="tariff-code-class">
                    <input type="hidden" name="CertificateTariffcodes[INDEX][id_tariffcodes_category]" class="tariff-code-category">
                    <input type="hidden" name="CertificateTariffcodes[INDEX][id_tariffcodes_product]" class="tariff-code-product">
                    
                    <input type="hidden" name="CertificateTariffcodes[INDEX][code_tariffcodes_class]" class="tariff-code-class">
                    <input type="hidden" name="CertificateTariffcodes[INDEX][code_tariffcodes_category]" class="tariff-code-category">
                    <input type="hidden" name="CertificateTariffcodes[INDEX][code_tariffcodes_product]" class="tariff-code-product">
                    
                    <input type="hidden" name="CertificateTariffcodes[INDEX][desc_tariffcodes_class]" class="tariff-description-class ">
                    <input type="hidden" name="CertificateTariffcodes[INDEX][desc_tariffcodes_category]" class="tariff-description-category">
                    <input type="hidden" name="CertificateTariffcodes[INDEX][desc_tariffcodes_product]" class="tariff-description-product">
                </td>
                <td>
                    <input type="text" name="CertificateTariffcodes[INDEX][description]" class="tariff-description" placeholder="<?= Yii::t('frontend',
                        'Type a description') ?>">
                </td>
                <td>
                    <?php
                    echo CustomHelper::generateCountryDropdownlist($form, $certificateTariffcodes, '[INDEX]id_country',
                        [
                            'class' => 'select-custom',
                            'id' => 'group-selection'
                        ]);
                    ?>
                </td>
                <td>
                    <input type="number" name="CertificateTariffcodes[INDEX][qty]" class="tariff-qty" placeholder="<?= Yii::t('frontend',
                        'Type a quantity') ?>">
                </td>
                <td>
                    <input type="text" name="CertificateTariffcodes[INDEX][value]" class="tariff-value" placeholder="<?= Yii::t('frontend',
                        'Type a value') ?>">
                </td>
                <td>
                    <input type="text" name="CertificateTariffcodes[INDEX][weight]" class="tariff-weight" placeholder="<?= Yii::t('frontend',
                        'Type a weight') ?>">
                </td>
            </tr>
        </table>
        <!-- End TariffCode -->
        
        <div class="containerType container-wrapper open" container-type-id="{CONTAINER-TYPE-ID}">
            <div class="container-head">
                <div class="container-head--title">
                    <p>{CONTAINER-TYPE-NAME}</p>
                </div>
                <div class="container-head--arrow">
                    <i class="fa fa-angle-down"></i>
                </div>
            </div>
            <div class="container-body">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-4 certificate-box--column">
                        <div class="container-body--group">
                            <div class="container-group--head">
                                <input type="hidden" name="table-container-type" class="table-container-type" value="{CONTAINER-TYPE-ID}">
                                <p><?= Lx::t('frontend', 'Container IDs '); ?><span><?= Lx::t('frontend',
                                            '(one per line)'); ?></span></p>
                                <div class="btns-wrapper">
                                    <button class="container-table--add" title="<?= Yii::t('frontend',
                                        'Add one container'); ?>">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                    <button class="container-table--addGroup" title="<?= Yii::t('frontend',
                                        'Add multiple containers'); ?>">
                                        <i class="fa fa-list-ul"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="container-group--body table-group">
                                <div class="input-wrapper">
                                    <input type="text" id="container--table-search" class="container--table-search">
                                </div>
                                <div class="container-table--wrapper">
                                    <table class="container-table">
                                        <tr class="container-table--head">
                                            <td></td>
                                            <td>
                                                <p><?= Lx::t('frontend', 'Container ID'); ?></p>
                                            </td>
                                            <td>
                                                <p><?= Lx::t('frontend', 'SOC'); ?></p>
                                            </td>
                                        </tr>
                                    </table>
                                    <div class="modal-wrapper multiple-line--wrapper">
                                        <div class="multiple-line--modal">
                                            <div class="modal-header">
                                                <p><?= Lx::t('frontend', 'Add Multiple Containers'); ?></p>
                                            </div>
                                            <div class="modal-body">
                                                <textarea rows="5"></textarea>
                                            </div>
                                            <div class="modal-footer">
                                                <button class="modal-close--btn" type="button"><?= Lx::t('frontend',
                                                        'Close'); ?></button>
                                                <button class="modal-submit--btn" type="button"><?= Lx::t('frontend',
                                                        'Save'); ?></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="no-results">
                                    <p><?= Lx::t('frontend', "There are no ID's to show"); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-8 certificate-box--column vehicles">
                        <div class="subcontainer">
                            <div class="container-group--head">
                                <div class="subcontainer-head--title">
                                    <p><?= Lx::t('frontend', 'Vehicles'); ?></p>
                                </div>
                            </div>
                            <div class="subcontainer-body ">
                                <div class="row">
                                    <div class="col-xs-12 col-sm-4 certificate-box--column">
                                        <div class="input-wrapper">
                                            <label for="container-vehicles"><?= Lx::t('frontend',
                                                    'Contains Vehicles'); ?></label>
                                            <select class="container-vehicles" type="text">
                                                <option value="2"><?= Lx::t('frontend', 'No'); ?></option>
                                                <option value="1"><?= Lx::t('frontend', 'Yes'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row vehicles-form">
                                    <div class="col-xs-12 col-sm-6 certificate-box--column">
                                        <div class="container-body--group">
                                            <div class="container-group--head">
                                                <p><?= Lx::t('frontend', 'New Vehicles'); ?></p>
                                            </div>
                                            <div class="container-group--body">
                                                <div class="row">
                                                    <div class="col-xs-12 col-sm-12 certificate-box--column">
                                                        <?php
                                                        echo $form->field($certificateContainersTypes,
                                                            "[INDEX]vehicles_new", [
                                                                'template' => '{label}{input}',
                                                                'options' => [
                                                                    'class' => 'input-wrapper',
                                                                ],
                                                                'labelOptions' => [
                                                                    'class' => '',
                                                                ]
                                                            ])->textarea([
                                                            'rows' => 3,
                                                        ]);
                                                        ?>
                                                    </div>
                                                    
                                                    <div class="col-xs-12 col-sm-4 certificate-box--column">
                                                        <div class="input-wrapper">
                                                            <label for="new-id--count">
                                                                <?= Lx::t('frontend', 'Count'); ?>
                                                            </label>
                                                            <span class="new-id--count" type="text"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 certificate-box--column">
                                        <div class="container-body--group">
                                            <div class="container-group--head">
                                                <p><?= Lx::t('frontend', 'Used Vehicles'); ?></p>
                                            </div>
                                            <div class="container-group--body">
                                                <div class="row">
                                                    <div class="col-xs-12 col-sm-12 certificate-box--column">
                                                        <?php
                                                        echo $form->field($certificateContainersTypes,
                                                            "[INDEX]vehicles_used", [
                                                                'template' => '{label}{input}',
                                                                'options' => [
                                                                    'class' => 'input-wrapper',
                                                                ],
                                                                'labelOptions' => [
                                                                    'class' => '',
                                                                ]
                                                            ])->textarea([
                                                            'rows' => 3,
                                                        ]);
                                                        ?>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-4 certificate-box--column">
                                                        <div class="input-wrapper">
                                                            <label for="used-id--count">
                                                                <?= Lx::t('frontend', 'Count'); ?></label>
                                                            <span class="used-id--count" type="text"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                    </div>
                    <div class="col-xs-12 certificate-box--column">
                        <div class="container-body--group">
                            <div class="container-group--head">
                                <p><?= Lx::t('frontend', 'Measurements'); ?></p>
                            </div>
                            <div class="container-group--body">
                                <div class="row">
                                    <div class="col-xs-12 col-sm-6 certificate-box--column">
                                        <?
                                        echo CustomHelper::generateInput($form, $certificateContainersTypes,
                                            "[INDEX]packages_nr", [
                                                'class' => 'input-wrapper packages-nr',
                                            ], '{label}{input}', [
                                                'class' => '',
                                            ], $disabledFields);
                                        ?>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 certificate-box--column">
                                        <?
                                        echo CustomHelper::generateInput($form, $certificateContainersTypes,
                                            "[INDEX]volume", [
                                                'class' => 'input-wrapper volume',
                                            ], '{label}{input}', [
                                                'class' => '',
                                            ], $disabledFields);
                                        ?>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 certificate-box--column">
                                        <div class="input-wrapper">
                                            <label for="container-wheight"><?= Lx::t('frontend', 'Weight'); ?></label>
                                            <div class="certificate-box--flex">
                                                <?php
                                                echo $form->field($certificateContainersTypes, "[INDEX]weight_unit", [
                                                    'template' => '{input}',
                                                    'options' => [
                                                        'tag' => false,
                                                    ]
                                                ])->dropDownList([
                                                    '0' => 'K',
                                                    '1' => 'T',
                                                ], [
                                                    'label' => false,
                                                    'class' => 'container-weight--unit',
                                                ]);
                                                
                                                echo CustomHelper::generateInput($form, $certificateContainersTypes,
                                                    "[INDEX]weight", [
                                                        'class' => 'name',
                                                        'tag' => false,
                                                    ], '{input}', [
                                                        'class' => 'container-weight',
                                                    ], $disabledFields);
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 certificate-box--column">
                                        <?
                                        echo CustomHelper::generateInput($form, $certificateContainersTypes,
                                            "[INDEX]freight", [
                                                'class' => 'input-wrapper freight',
                                            ], '{label}{input}', [
                                                'class' => '',
                                            ], $disabledFields);
                                        
                                        echo $form->field($certificateContainersTypes, '[INDEX]id_container_type', [
                                            'template' => '{input}',
                                            'options' => [
                                                'tag' => false,
                                            ],
                                        ])->hiddenInput([
                                            'class' => 'hidden-container-type',
                                        ]);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xs-12 certificate-box--column btns-wrapper">
                        <button type="button" class="container-remove--btn flex-btn">
                            <span>Remove</span>
                        </button>
                        <div class="submit-wrapper">
                            <div class="submit-wrapper">
                                <?php
                                echo Html::submitButton('<i class="fa fa-save"></i><span>Submit</span>', [
                                    'class' => 'container-submit--btn flex-btn',
                                ]);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <table class="container">
            <tr>
                <td>
                    <span class="container-table--remove">
                        <i class="fa fa-trash"></i>
                    </span>
                </td>
                <td>
                    <?
                    echo $form->field($certificateContainers, '[INDEX]nr', [
                        'template' => '{input}',
                        'options' => [
                            'tag' => false,
                            'class' => '',
                        ]
                    ])->textInput(['class' => '']);
                    ?>
                </td>
                <td>
                    <?
                    echo $form->field($certificateContainers, '[INDEX]soc', [
                        'template' => '{input}',
                        'options' => [
                            'tag' => false,
                        ]
                    ])->checkbox([
                        'label' => false,
                    ]);
                    
                    
                    echo $form->field($certificateContainers, '[INDEX]id_container_type', [
                        'template' => '{input}',
                        'options' => [
                            'tag' => false,
                        ],
                    ])->hiddenInput([
                        'class' => 'hidden-container-type',
                    ]);
                    ?>
                </td>
            </tr>
        </table>
        
        <div class="du-modal--container">
            <div class="file-modal--wrapper--container">
                <div class="file-modal">
                    <span class="close-modal"><i class="fa fa-close" aria-hidden="true"></i></span>
                    <div class="row clearfix">
                        <div class="col-xs-12 certificate-box--column">
                            <p class="modal-title"><?= Lx::t('frontend', 'Upload File'); ?></p>
                        </div>
                        <div class="col-xs-12 name">
                            <?php
                            echo CustomHelper::generateInput($form, $certificateDus, '[INDEX]name', [
                                'class' => 'input-wrapper input-wrapper-name name',
                                'tag' => 'div',
                            ], '{label}{input}', [
                                'class' => 'certificate-du--name',
                                'maxlength' => 18, //lame, eh?
                            ], $disabledFields);
                            ?>
                        </div>
                        <div class="col-xs-12 file">
                            <?php
                            echo $certificateDuFile = $form->field($certificateDus, "[INDEX]path", [
                                'options' => [
                                    'class' => 'input-wrapper input-wrapper-file file',
                                    'tag' => 'div',
                                ],
                            
                            ])->fileInput(array_merge($formDocsOptions, in_array('upload_du', $disabledFields) ? [
                                'disabled' => '',
                                'class' => 'input-field certificate-du--path',
                            ] : [
                                'class' => 'input-field certificate-du--path',
                            ]));
                            ?>
                        </div>
                        <div class="col-xs-12 certificate-box--column clearfix">
                            <button type="button" class="file-modal--submit"><?= Lx::t('frontend', 'Save'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <li class="ft-item containers-item cloneTariffcode mobile-align">
            <div class="numb">00</div>
            <div class="inputs-wrapper">
                <div class="flex-it">
                    <div class="col-md-2">
                        <?php echo CustomHelper::generateDropdownlist($form, $certificateTariffcodes,
                            "[INDEX]id_tariffcodes_class", [
                                'class' => 'form-control tariffcode-class',
                                'prompt' => Yii::t('frontend', 'Pick a class'),
                                'options' => $tariffcodesClassesOptions
                            ], $tariffcodesClassesItems); ?>
                        <?php echo CustomHelper::generateInput($form, $certificateTariffcodes,
                            "[INDEX]code_tariffcodes_class", array_merge($inputOptions, ['class' => 'hidden']),
                            $inputTemplate, array_merge($inputFieldOptions, ['class' => 'code']), $disabledFields); ?>
                        <?php echo CustomHelper::generateInput($form, $certificateTariffcodes,
                            "[INDEX]desc_tariffcodes_class", array_merge($inputOptions, ['class' => 'hidden']),
                            $inputTemplate, array_merge($inputFieldOptions, ['class' => 'description']),
                            $disabledFields); ?>
                    </div>
                    <div class="col-md-2">
                        <?php echo CustomHelper::generateDropdownlist($form, $certificateTariffcodes,
                            "[INDEX]id_tariffcodes_category", [
                                'class' => 'form-control tariffcode-category',
                                'prompt' => Yii::t('frontend', 'Pick a category'),
                            ], []); ?>
                        <?php echo CustomHelper::generateInput($form, $certificateTariffcodes,
                            "[INDEX]code_tariffcodes_category", array_merge($inputOptions, ['class' => 'hidden']),
                            $inputTemplate, array_merge($inputFieldOptions, ['class' => 'code']), $disabledFields); ?>
                        <?php echo CustomHelper::generateInput($form, $certificateTariffcodes,
                            "[INDEX]desc_tariffcodes_category", array_merge($inputOptions, ['class' => 'hidden']),
                            $inputTemplate, array_merge($inputFieldOptions, ['class' => 'description']),
                            $disabledFields); ?>
                    </div>
                    <div class="col-md-2">
                        <?php echo CustomHelper::generateDropdownlist($form, $certificateTariffcodes,
                            "[INDEX]id_tariffcodes_product", [
                                'class' => 'form-control tariffcode-product',
                                'prompt' => Yii::t('frontend', 'Pick a product')
                            ], []); ?>
                        <?php echo CustomHelper::generateInput($form, $certificateTariffcodes,
                            "[INDEX]code_tariffcodes_product", array_merge($inputOptions, ['class' => 'hidden']),
                            $inputTemplate, array_merge($inputFieldOptions, ['class' => 'code']), $disabledFields); ?>
                        <?php echo CustomHelper::generateInput($form, $certificateTariffcodes,
                            "[INDEX]desc_tariffcodes_product", array_merge($inputOptions, ['class' => 'hidden']),
                            $inputTemplate, array_merge($inputFieldOptions, ['class' => 'description']),
                            $disabledFields); ?>
                    </div>
                    <div class="col-md-2">
                        <?php
                        echo CustomHelper::generateInput($form, $certificateTariffcodes, "[INDEX]qty", $inputOptions,
                            $inputTemplate, array_merge($inputFieldOptions, ['class' => 'qty']), $disabledFields);
                        ?>
                    </div>
                    <div class="col-md-2">
                        <?php
                        echo CustomHelper::generateInput($form, $certificateTariffcodes, "[INDEX]value", $inputOptions,
                            $inputTemplate, $inputFieldOptions, $disabledFields);
                        ?>
                    </div>
                    <div class="col-md-2">
                        <?php
                        echo CustomHelper::generateInput($form, $certificateTariffcodes, "[INDEX]weight", $inputOptions,
                            $inputTemplate, array_merge($inputFieldOptions, ['class' => 'weight']), $disabledFields);
                        ?>
                    </div>
                
                </div>
                <div class="flex-it">
                    <div class="col-md-3 container-data">
                        <div class="group form-group with-value">
                            <input id="certificateTariffcodes-tariffcode[]" name='tariffcode[INDEX]' class='tariffcode' type="text" value="<?php
                            ?>"/>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="group with-value success">
                            <?php
                            echo CustomHelper::generateCountryDropdownlist($form, $certificateTariffcodes,
                                "[INDEX]id_country", ['class' => 'select-custom']);
                            ?>
                        </div>
                    </div>
                    <div class="col-md-6 container-data">
                        <?php
                        echo CustomHelper::generateInput($form, $certificateTariffcodes, "[INDEX]description",
                            $inputOptions, $inputTemplate, array_merge($inputFieldOptions, [
                                'data-container' => 'containers40-items',
                                'data-li    nked-input' => 'certificate-lading_40ft_nr',
                                'data-total-id' => 'containers40-total'
                            ]), $disabledFields);
                        ?>
                    </div>
                </div>
            </div>
            <div class="icon-wrapper delete-item"><i class="fa fa-times" aria-hidden="true"></i></div>
        </li>
    </div>

<?php
$guichetINDEX = !empty($certificate->getCertificateGuichet()
    ->max('id_certificate_guichet')) ? $certificate->getCertificateGuichet()->max('id_certificate_guichet') + 1 : 0;
$transhipmentsINDEX = !empty($certificate->getCertificateTranshipments()
    ->max('id_certificate_transhipment')) ? $certificate->getCertificateTranshipments()
        ->max('id_certificate_transhipment') + 1 : 0;
$chassisINDEX = !empty($certificate->getCertificateChassis()
    ->max('id_certificate_chassi')) ? $certificate->getCertificateChassis()->max('id_certificate_chassi') + 1 : 0;
$containersINDEX = !empty($certificate->getCertificateContainers()
    ->max('id_certificate_container')) ? $certificate->getCertificateContainers()
        ->max('id_certificate_container') + 1 : 0; // this is a common table to containers20 and containers40 therefore common INDEX
$tariffcodesINDEX = !empty($certificate->getcertificateTariffcodes()
    ->max('id_certificate_tariffcode')) ? $certificate->getcertificateTariffcodes()
        ->max('id_certificate_tariffcode') + 1 : 0;

$initCommands = is_null($certificate->id_certificate) ? 'disableSave()' : '';
//disable Init Commands
$initCommands = '';

$user = Yii::$app->user->identity;

$flowAgentsPermissions = [
    'manager' => User::TYPE_MANAGER,
    'maker' => User::TYPE_MAKER,
    'invoicer' => User::TYPE_INVOICER,
];

$isNewRecord = $certificate->isNewRecord ? 'true' : 'false';

$isValid = $weightTariffcodes == $weightContainers ? 'true' : 'false';

$datepicker = <<<JS

function disableSave(disable = true){
	
	$('#save-Certificate').removeAttr('disabled');
    $('#save-Certificate-top').removeAttr('disabled');
	if(disable){
	    $('#save-Certificate').attr('disabled',disable);
	    $('#save-Certificate-top').attr('disabled',disable);
    }
}

var guichetINDEX = $guichetINDEX;
var transhipmentINDEX = $transhipmentsINDEX;
var chassisINDEX = $chassisINDEX;
var containersINDEX = $containersINDEX;
var tariffcodesINDEX = $tariffcodesINDEX;
let isValid = $isValid;

function cloneNode(node, number, index, target){
   for(var i=1; i<=number;i++){
        var newElem = $('.'+node+':last').clone(true, true);
         //change INDEX to var chassisINDEX to have proper Yii 2 validation
         newElem.find(':input').each(function (index) {
             $(this).attr('name', $(this).attr('name').replace('INDEX',index));
             $(this).attr('id', $(this).attr('id').replace('index',index));
         });
         newElem.removeClass('node');
         newElem.removeClass('hidden');
         newElem.appendTo(target);
         newElem.focus();
         index++;
    }
}


function displayFlowSelector(show = true) {
    let flowSelectorTab = $('#flow-selector')

    if(show == 1){
        flowSelectorTab.show()
    }else{
        flowSelectorTab.hide()
        flowSelectorTab.val('')
    }
}

$('#city').change(function(){
    let isForeign = $(this).val() == 3
    
    displayFlowSelector(false)
    if(isForeign)
        displayFlowSelector()
})

$(document).ready(function () {
    //INIT Command
    {$initCommands}
    
    // Start Foreign rules
    let isNewCertificate = $isNewRecord;
    let isValidTypeUser = ($.inArray({$user->type}, [{$flowAgentsPermissions['manager']},{$flowAgentsPermissions['maker']},{$flowAgentsPermissions['invoicer']}]) >= 0) && !isNewCertificate

    displayFlowSelector(false)
    if(isValidTypeUser)
        displayFlowSelector()
     
     // End Foreing
    
    $('.add-more--btn.guichet').click(function (e) {
        e.preventDefault();
        var newElem = $('.cloneGuichet:last').clone(true, true);
        //change INDEX to var cloneGuichetINDEX to have proper Yii 2 validation
        newElem.find(':input').each(function (index) {
            $(this).attr('name', $(this).attr('name').replace('INDEX', guichetINDEX));
            $(this).attr('id', $(this).attr('id').replace('index',guichetINDEX));
        });
        newElem.removeClass('cloneGuichet');
        newElem.removeClass('hidden');
        newElem.appendTo('.guichet-items');

        guichetINDEX++;

        var totalGuichets = $('ul.guichet-items li').length;
        totalGuichets = totalGuichets < 10 ? "0" + totalGuichets : totalGuichets;
        newElem.find('.numb').text(totalGuichets);
        $(".guichets-total").text(totalGuichets);
    });

    $('.add-more--btn.transhipment').click(function (e) {
        e.preventDefault();
        var newElem = $('.cloneTranshipment:last').clone(true, true);
        //change INDEX to var transhipmentINDEX to have proper Yii 2 validation
        newElem.find(':input').each(function (index) {
            $(this).attr('name', $(this).attr('name').replace('INDEX', transhipmentINDEX));
            $(this).attr('id', $(this).attr('id').replace('index',transhipmentINDEX));
        });
        newElem.removeClass('cloneTranshipment');
        newElem.removeClass('hidden');
        newElem.appendTo('.transhipment-items');
        newElem.find('.datetimepicker').datetimepicker({
            format: 'YYYY-MM-DD',
        });

        $('#certificatetranshipments-'+transhipmentINDEX+'-harbor').typeahead(null, {
            name: 'transhipment-'+transhipmentINDEX+'-source',
            display: 'value',
            source: citiesFunc($("#certificatetranshipments-"+transhipmentINDEX+"-id_country")),
            limit: 10,
        });

        transhipmentINDEX++;

        var totalTranshipments = $('ul.transhipment-items li').length;
        totalTranshipments = totalTranshipments < 10 ? "0" + totalTranshipments : totalTranshipments;
        newElem.find('.numb').text(totalTranshipments);
        $(".transhipments-total").text(totalTranshipments);
    });

    $('.add-more--btn.chassi').click(function (e) {
        e.preventDefault();
        var newElem = $('.cloneChassis:last').clone(true, true);
        //change INDEX to var chassisINDEX to have proper Yii 2 validation
        newElem.find(':input').each(function (index) {
            $(this).attr('name', $(this).attr('name').replace('INDEX',chassisINDEX));
        });
        newElem.removeClass('cloneChassis');
        newElem.removeClass('hidden');
        newElem.appendTo('.chassis-items');
        newElem.focus();

        chassisINDEX++;

        var totalChassis = $('ul.chassis-items li').length;
        $('#certificate-lading_chassis_nr').val(totalChassis);
        totalChassis = totalChassis < 10 ? "0" + totalChassis : totalChassis;
        newElem.find('.numb').text(totalChassis);
        $(".chassis-total").text(totalChassis);
    });

    $('.add-more--btn.container20').click(function (e) {
        e.preventDefault();
        var newElem = $('.cloneContainer20:last').clone(true, true);
        //change INDEX to var containersINDEX to have proper Yii 2 validation
        newElem.find(':input').each(function (index) {
            $(this).attr('name', $(this).attr('name').replace('INDEX',containersINDEX));
        });
        newElem.removeClass('cloneContainer20');
        newElem.removeClass('hidden');
        newElem.appendTo('.containers20-items');
        newElem.focus();

        containersINDEX++;

        var totalContainers20 = $('ul.containers20-items li').length;
        $('#certificate-lading_20ft_nr').val(totalContainers20);
        totalContainers20 = totalContainers20 < 10 ? "0" + totalContainers20 : totalContainers20;
        newElem.find('.numb').text(totalContainers20);
        $(".containers20-total").text(totalContainers20);
    });

    $('.add-more--btn.container40').click(function (e) {
        e.preventDefault();
        var newElem = $('.cloneContainer40:last').clone(true, true);
        //change INDEX to var containersINDEX to have proper Yii 2 validation
        newElem.find(':input').each(function (index) {
            $(this).attr('name', $(this).attr('name').replace('INDEX',containersINDEX));
        });
        newElem.removeClass('cloneContainer40');
        newElem.removeClass('hidden');
        newElem.appendTo('.containers40-items');
        newElem.focus();

        containersINDEX++;

        var totalContainers40 = $('ul.containers40-items li').length;
        $('#certificate-lading_40ft_nr').val(totalContainers40);
        totalContainers40 = totalContainers40 < 10 ? "0" + totalContainers40 : totalContainers40;
        newElem.find('.numb').text(totalContainers40);
        $(".containers40-total").text(totalContainers40);
    });

    $('.add-more--btn.tariffcodes').click(function (e) {
        e.preventDefault();
        var newElem = $('.cloneTariffcode:last').clone(true, true);
        console.log(newElem);

        //change INDEX to var containersINDEX to have proper Yii 2 validation
        newElem.find(':input').each(function (index) {
            $(this).attr('name', $(this).attr('name').replace('INDEX',tariffcodesINDEX));
        });
        newElem.removeClass('cloneTariffcode');
        newElem.removeClass('hidden');
        newElem.appendTo('.tariffcodes-items');
        newElem.focus();

        containersINDEX++;

        var totalTariffcodes = $('ul.tariffcodes-items li').length;
        newElem.find('.numb').text(totalTariffcodes);
        $(".tariffcodes-total").text(totalTariffcodes);
    });

     $(document).on('click', '.delete-item', function(){
        $(this).closest('li').remove();

        var thisSiblingsInputElem =  $(this).siblings('input');
        var container = thisSiblingsInputElem.data('container');
        var totalId = thisSiblingsInputElem.data('total-id');

        if (typeof container === 'undefined') {
            var thisclosestInputElem = $(this).closest('.containers-item').find('.container-data').find('input');
            container = thisclosestInputElem.data('container');
            totalId = thisclosestInputElem.data('total-id')
        }

        var containerLiElems = $("."+container + ' li');
        var newLength = containerLiElems.length;
        var linkedInput = $("#"+ $(this).siblings('input').data('linked-input'));
        linkedInput.val(newLength);
        newLength = newLength < 10 ? "0" + newLength : newLength;
        $("#"+totalId).text(newLength);

        var i = 1;
        containerLiElems.each(function() {
            $(this).find('.numb').text((i < 10 ? "0" + i : i));
            i++;
        });
    });


    //Clone product
    $('.products-add--btn').click(function (e) {
        e.preventDefault();
        let tableProduct = $('.product-table tbody');
        let rowElement = $('.clone-tariffcode-row:last tr');
        let totalProduct = $('.products-total').html();
        
        totalProduct = !isNaN(totalProduct)? parseFloat(totalProduct):0;
        
        //prepend in table
        rowElement.clone().prependTo('.product-table tbody');
        
        //Total Product
        totalProduct++;
        $('.products-total').html(totalProduct);

        //change INDEX to var containersINDEX to have proper Yii 2 validation
        tableProduct.find(':input').each(function (index) {
            $(this).attr('name', $(this).attr('name').replace('INDEX',tariffcodesINDEX));
        });
        
        tariffcodesINDEX++;
    });
});
JS;

$this->registerJS($datepicker);
$weightTotalErrorMsg = Lx::t('frontend','Please check your weight totals of Containers and HS Codes. They do not match.');
if (Yii::$app->controller->action->id == 'create' || true) {
    
    $msgTypeError = Lx::t('frontend', 'Fill correctly the field ');
    
    $validForm = <<<JS
		let time = 999999;
        	$('.alert-danger').each(function( index ) {
	            $(this).remove();
	        });
JS;

if ($action == 'create') {
    $validForm .= <<<JS
        	$('.to-valid-file').each(function( index ) {
        		let file = $(this).find('.input-file');
        		
        		console.log(file.val());
        		
        		if(file.val() == ''){
        			let nameField = $(this).find('label .language-item').text();
        			
        			$.toaster({
		                priority : 'danger',
		                title : 'Type Error',
		                message : '$msgTypeError upload '+'<b>'+nameField+'</b>',
		                time : time
		            });
        		}
        	});
JS;
}
    $validForm .= <<<JS
        	$('.to-valid').each(function( index ) {
        		
        		let field = $(this).find('.form-control');
        		
	            if(field.val() == ''){
	            	let parentNode = field.parent();
	            	
	            	parentNode.addClass('has-error');
	            	let nameField = parentNode.find('.control-label').text();
	            	
	            	$.toaster({
		                priority : 'danger',
		                title : 'Type Error',
		                message : '$msgTypeError'+'<b>'+nameField+'</b>',
		                time : time
		            });
	            }
	        });
        	
        	
        	/*
            let goods_loading_date = $("#certificate-goods_loading_date");
        	let goods_deliveryestimate_date = $("#certificate-goods_deliveryestimate_date");
			let upload_bill_lading = $("#doc-upload_bill_lading");
			let upload_commercial_invoice = $("#doc-upload_commercial_invoice");
			let upload_freight_invoice = $("#doc-upload_freight_invoice");
			let upload_draft_request_signed = $("#doc-upload_draft_request_signed");
			let vessel_voyage_nr = $("#certificate-vessel_voyage_nr");
			
			let vessel_bl_nr = $("#certificate-vessel_bl_nr");
			let cost_invoice_value = $("#certificate-cost_invoice_value");
			let forwarding_agent = $("#certificate-forwarding_agent");
			let exporter_name = $("#certificate-exporter_name");
			let exporter_address = $("#certificate-exporter_address");
			let importer_vat = $("#certificate-importer_vat");
			let importer_name = $("#certificate-importer_name");
			let importer_address = $("#certificate-importer_address");
			let vessel_name = $("#certificate-vessel_name");
			let vessel_shipping_line = $("#certificate-vessel_shipping_line");
			
			let hasGoodsLoadingDate = hasValue(goods_loading_date);
        	let hasGoodsDeliveryestimateDate = hasValue(goods_deliveryestimate_date);
			let hasUploadBillLading = hasValue(upload_bill_lading);
			let hasUploadCommercialInvoice = hasValue(upload_commercial_invoice);
			let hasUploadFreightInvoice = hasValue(upload_freight_invoice);
			let hasUploadDraft_requestSigned = hasValue(upload_draft_request_signed);
			let hasVesselVoyageNr = hasValue(vessel_voyage_nr);
			let hasVesselBlNr = hasValue(vessel_bl_nr);
			let hasCostInvoiceValue = hasValue(cost_invoice_value);
			let hasForwardingAgent = hasValue(forwarding_agent);
			let hasExporterName = hasValue(exporter_name);
			let hasExporterAddress = hasValue(exporter_address);
			let hasImporterVat = hasValue(importer_vat);
			let hasImporterName = hasValue(importer_name);
			let hasImporterAddress = hasValue(importer_address);
			let hasVesselName = hasValue(vessel_name);
			let hasVesselShippingLine = hasValue(vessel_shipping_line);
            */
JS;

    
	
    $createJs = <<<JS
    
    function hasValue(value) {
		return value != ''? true:false;
    }
    
    $(document).ready(function () {
        var isValid = true;
        var invalidElement = null;
        var totalWeight = 0;
        var totalQty = 0;

        $("#save-Certificate, #save-Certificate-top, .container-submit--btn").click(function(e) {
			
        	$validForm
        	
            isValid = true;
            var form = $('#new-certificate--form');

            /* finding out EMPTY REQUIRED inputs */
            $('input,textarea,select').filter('[required]:visible').each(function() {
                if ($(this).val() === '') {
                    isValid = false;
                    // Show Error Fields
                    if(invalidElement == null) {
                        invalidElement = $(this);
                    }
                }
            });

            /* finding out variants of EMPTY REQUIRED inputs */
            if(invalidElement == null) {
                $('input,textarea,select').filter('[aria-required]:visible').each(function() {
	                if ($(this).val() === '' && !$(this).hasClass('tt-hint') ) {
	                    isValid = false;
	                    if(invalidElement == null) {
	                        invalidElement = $(this);
	                    }
	                }
	            });
            }
            
            /* focusing on the first INVALID input */
            if($('#new-certificate--form').find('.has-error').length > 0 || !isValid) {
                if(isValid){
                    $("html, body").animate({scrollTop:$('#new-certificate--form').find('.has-error').first().offset().top}, 500);
                    $('#new-certificate--form').find('.has-error').first().focus();
//                    console.log("1.scrolling to");
//                    console.log( $('#new-certificate--form').find('.has-error').first() );

                } else {
                    $("html, body").animate({scrollTop:invalidElement.offset().top}, 500);
                    invalidElement.focus();
                }
                $(this).attr('disabled', 'disabled');
            }
            
            setTimeout(function(){
                $("#save-Certificate").removeAttr('disabled');
                $("#save-Certificate-top").removeAttr('disabled');
            }, 1000);

            isValidWeight = false;
            isValidQty = true;

            /* updating totalQty */
            totalQty = 0;
            $(".tariffcodes-items .qty").each( function() {
                var currQty = Math.round($(this).val() * 100) / 100;
                totalQty += currQty;
                totalQty = Math.round(totalQty * 100) / 100;
            });

            /* updating totalWeight */
            totalWeight = 0;
            $(".tariffcodes-items .weight").each( function() {
                var currWeight = Math.round($(this).val() * 100) / 100;
                console.log( currWeight );
                totalWeight += currWeight;
                totalWeight = Math.round(totalWeight * 100) / 100;
            });
            
            let totalWeightContainers = 0;
            $(".container-weight").each( function() {
                let containerWeightUnit = $(this).closest('.container-group--body').find('.container-weight--unit').val();
                let unitMultiplier = 1;
                
                if(containerWeightUnit == 1){
                    unitMultiplier = 1000;
                }
                
                let containerWeight = Math.round($(this).val() * 100) / 100 * unitMultiplier;
                console.log(unitMultiplier);
                console.log("container weight: " + containerWeight);
                console.log("containerUnitValue" + containerWeightUnit);
                totalWeightContainers += containerWeight;
            });
           
            let totalWeightTariffs = 0;
            $(".tariff-weight").each( function() {
                let tariffWeight = Math.round($(this).val() * 100) / 100;
                totalWeightTariffs += tariffWeight;
            });
            

            console.log('total weight = ', totalWeightContainers);
            console.log('total Tariff = ',totalWeightTariffs);
            if(totalWeightContainers == totalWeightTariffs){
                isValidWeight = true;
            } else {
                $(".totals-value--weightdiff").html( (totalWeightContainers - totalWeightTariffs) + " K");
                $.toaster({
                    priority : 'danger',
                    title : 'Type Error',
                    message : '$weightTotalErrorMsg',
                    time : time
                });
            }
            
            //isValidWeight = false;
            
            /* validate total packages
            if( totalQty != $("#certificate-lading_packages_nr").val()*1){
                console.log("check total qty does not match qty at point 07");
                $(this).attr('disabled', 'disabled');
                invalidElement = $("#certificate-lading_weight");
                $("#certificate-lading_packages_nr").focus();
                $("#certificate-lading_packages_nr").css("color", "red");
                isValidQty = false;
            } else {
                $("#certificate-lading_packages_nr").css("color", "black");
                isValidQty = true;
            }
            */
            /* validate total packages
            if( totalWeight != $("#certificate-lading_weight").val()*1){
                console.log("check total weights does not match weight at point 07");
                console.log( $("#certificate-lading_weight").val() );
                console.log( totalWeight );

                $(this).attr('disabled', 'disabled');
                invalidElement = $("#certificate-lading_weight");
                $("#certificate-lading_weight").focus();
                $("#certificate-lading_weight").css("color", "red");
                isValidWeight = false;
            } else {
                $("#certificate-lading_weight").css("color", "black");
                isValidQty = true;
            }
            */
            
            if(isValid){
                if( totalQty != $("#certificate-lading_packages_nr").val()){
                    $("#certificate-lading_packages_nr").focus();
                }

                if( totalWeight != $("#certificate-lading_weight").val()){
                	$("#certificate-lading_weight").focus();
                }
            }

            isValid = isValid * isValidWeight * isValidQty;
            
            if( isValid ){
            	
                form.submit();
            }
        });

        $('#new-certificate--form').on('beforeSubmit', function(event, jqXHR, settings) {
            var form = $(this);
            
            //kill all the CLONES and HELPERS
            $(".form-clones--helpers").remove();
            
            if (form.find('.has-error').length === 0 && isValid == true) {
                $(this).attr('disabled', 'disabled');
                $("#save-Certificate").text('...');
                $("#save-Certificate-top").text('...');
                console.log("form is being submitted brah");
                return true;
            } else {
                console.log("form is invalid brah");
                $("#save-Certificate").removeAttr('disabled');
                $("#save-Certificate-top").removeAttr('disabled');
                return false;
            }
        });
    });

    (function() {
        window.formCheck = formCheck;
        function formCheck() {
            var fields = $(".required")
                .find("select, textarea, input").serializeArray();
          
            $.each(fields, function(i, field) {
            if (!field.value)
              {alert(field.name + ' is required');}
            });
            console.log(fields);
        }
    })();
JS;
    $this->registerJS($createJs);
}

$urlCitySearch = Url::to(['city/search-city']);
$urlHarborSearch = Url::to(['city/search-harbor']);
$tariffcodesCategorySearch = Url::to(['certificates/search-category']);
$tariffcodesProductSearch = Url::to(['certificates/search-product']);

$tariffcodeCheck = Url::to(['certificates/check-tariffcode']);

$bloodhoundJS = <<<JS

// Tariff Codes Begin
function inputError(element) {
    let description = element.parent().parent().find('.tariff-description');

	element.attr('style','border: 1px solid #FF6d06')
    element.val('')
    description.val('')
}

function inputSuccess(element) {
	element.removeAttr('style','border: 1px solid #FF6d06')
}

$('.tariffcode').keyup(function(){
    getTariffCode(this);
})

function getTariffCode(tariff, allow=false) {
	let countKeysTariffcode = $(tariff).val().length;
    let hasCode = ($.inArray(countKeysTariffcode, [2,4]) != -1 || countKeysTariffcode >= 8) || allow
    let element = $(tariff)
    
    let codeClass = element.parent().parent().find('.tariff-code-class')
    let codeCategory = element.parent().parent().find('.tariff-code-category')
    let codeProduct = element.parent().parent().find('.tariff-code-product')
    
    let descriptionClass = element.parent().parent().find('.tariff-description-class')
    let descriptionCategory = element.parent().parent().find('.tariff-description-category')
    let descriptionProduct = element.parent().parent().find('.tariff-description-product')
    
    if(hasCode){
        $.getJSON("$tariffcodeCheck", {
            value: $(tariff).val()
        }).done( function (data) {
        	disableSave()
        	inputSuccess(element)
        	
        	if(typeof data.error === 'undefined'){
	            let idClass = data.tariffcodes.id_class
	            let idCategory = data.tariffcodes.id_category
	            let idProduct = data.tariffcodes.id_product
	            let isSelected = false
	            
	            if(typeof data.tariffcodeClass !== "undefined"){
		            $.each(data.tariffcodeClass, function( i, item ) {
                        isSelected = idClass == item.id_tariffcodes_class
                        if(isSelected){
                            codeClass.val(item.code);
                            descriptionClass.val(item.description);
                        }
					})
				}
				
				if(typeof data.tariffcodeCategory !== "undefined"){
		            $.each(data.tariffcodeCategory, function( i, item ) {
                        isSelected = idCategory == item.id_tariffcodes_category
                        if(isSelected){
                        	codeCategory.val(item.code);
                            descriptionCategory.val(item.description);
                        }
					})
				}
	            
	            if(typeof data.tariffcodeProduct !== "undefined"){
		            $.each(data.tariffcodeProduct, function( i, item ) {
                        isSelected = idProduct == item.id_tariffcodes_product
                        if(isSelected){
                        	codeProduct.val(item.code);
                            descriptionProduct.val(item.description);
                        }
					})

				}
				
	            if(countKeysTariffcode >= 8){
				    disableSave(false)
				}
			}else{
        		//ERROR
        		inputError(element)
			}
        })
    }
}
// Tariff Codes End


var citiesFunc = function(target){
    var bloody = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
        url: "$urlCitySearch",
        prepare: function(search,settings){
            settings.url += "?q="+search;
            settings.url += "&country=" + target.val();
            return settings;
        },
        rateLimitWait: 250,
        rateLimitBy: 'throtle',
      }
    });
    return bloody;
}

var harborsFunc = function(target){
    var bloody = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
        url: "$urlHarborSearch",
        prepare: function(search,settings){
            settings.url += "?q="+search;
            settings.url += "&country=" + target.val();
            return settings;
        },
        rateLimitWait: 250,
        rateLimitBy: 'throtle',
      }
    });
    return bloody;
}

$('#certificate-exporter_id_city').typeahead(null, {
    name: 'exporter-city',
    display: 'value',
    source: citiesFunc($("#certificate-exporter_id_country")),
    limit: 10,
    min: 3,
    hint: true,
    highlight:true,
});

$('#certificate-importer_id_city').typeahead(null, {
    name: 'importer-city',
    display: 'value',
    source: citiesFunc($("#certificate-importer_id_country")),
    limit: 10,
    min: 3,
    hint: true,
    highlight:true,
});

$('#certificate-goods_loading_harbor').typeahead(null, {
    name: 'importer-city',
    display: 'value',
    source: citiesFunc($("#certificate-goods_loading_id_country")),
    limit: 10,
    min: 3,
    hint: true,
    highlight:true,
});

function changeCC(opt){
    console.log("changing the import bank settings to option:");
    console.log(opt);
    if(opt == 1){
        $("#certificate-importer_bank").attr('disabled',false);
    }else{
        $("#certificate-importer_bank").val('')
        $("#certificate-importer_bank").attr('disabled',true);
    }
}

$("#certificate-importer_cc").change(function() {
    changeCC(this.value);
})

//changeCC($("#certificate-importer_cc").val());

$(".form-item").on('change', '.tariffcode-class', function() {
    var select =  $(this).parent().parent().find('select.tariffcode-product');
    select.html('');
    var select =  $(this).parent().parent().find('select.tariffcode-category');
    select.html('');
    let tariffCode = $(this).parent().parent().parent().parent().find('input.tariffcode')
    
    tariffCode.val('')
    tariffCode.val($(this).find('option:selected').data('code'))
    
    var code = $(this).parent().find('input.code');
    code.val( $(this).find('option:selected').data('code') );
    var desc = $(this).parent().find('input.description');
    desc.val( $(this).find('option:selected').text() );

    $.getJSON("$tariffcodesCategorySearch", {
        class: $(this).val()
    }).done( function (data ) {
        if(data){
            $.each( data, function( i, item ) {
                select.append("<option value='"+item.id_tariffcodes_category+"' data-code='"+item.code+"'>"+item.description+"</option>");
            });
        }
    });
});

$(".form-item").on('change', '.tariffcode-category', function() {
    var select = $(this).parent().parent().find('select.tariffcode-product');
    select.html('');
    let tariffCode = $(this).parent().parent().parent().parent().find('input.tariffcode')
    let valueTariffCode = tariffCode.val()
    let classCode = $(this).parent().parent().find('select.tariffcode-class').find('option:selected').data('code')
    
    tariffCode.val('')
    
    if($(this).find('option:selected').data('code') != null){
        tariffCode.val(classCode+''+$(this).find('option:selected').data('code'))
    }else{
    	disableSave()
    	tariffCode.val(classCode)
    }

    var code = $(this).parent().find('input.code');
    code.val( $(this).find('option:selected').data('code') );
    var desc = $(this).parent().find('input.description');
    desc.val( $(this).find('option:selected').text() );

    $.getJSON("$tariffcodesProductSearch", {
        category: $(this).val()
    }).done( function (data ) {
        if(data){
            $.each( data, function( i, item ) {
                select.append("<option value='"+item.id_tariffcodes_product+"' data-code='"+item.code+"'>"+item.description+"</option>");
            });
        }
    });
});

$(".form-item").on('change', '.tariffcode-product', function() {
    var code = $(this).parent().find('input.code');
    code.val( $(this).find('option:selected').data('code') );
    var desc = $(this).parent().find('input.description');
    desc.val( $(this).find('option:selected').text() );
    let tariffCode = $(this).parent().parent().parent().parent().find('input.tariffcode')
    let valueTariffCode = tariffCode.val()
    let classCode = $(this).parent().parent().find('select.tariffcode-class').find('option:selected').data('code')
    let productCode = $(this).parent().parent().find('select.tariffcode-category').find('option:selected').data('code')
    
    tariffCode.val('')
    if($(this).find('option:selected').data('code') != null){
    	/*element.parent().removeClass('error')
        element.parent().addClass('success')*/
    	disableSave(false)
        tariffCode.val(classCode+''+productCode+''+$(this).find('option:selected').data('code'))
    }else{
    	disableSave()
    	tariffCode.val(classCode+''+productCode)
    }
});


JS;
$fillFildText = Lx::t('frontend', 'fill correctly in the field');
$toasterJS = <<<JS
	$('button[type="submit"').click(function(event) {
		
        let time = 999999;
        $('.alert-danger').each(function( index ) {
            $(this).remove();
        });

		
		$('input').each(function( index ) {
            let isRequired = $(this).attr('aria-required') == 'true';

            if(isRequired && $(this).val() == ''){
                let label = $(this).parent().find('.control-label').text();
                
                $.toaster({
                    priority : 'danger',
                    title : 'Type Error',
                    message : '$fillFildText '+label,
                    time : time
                });
            }
		});
		
		$('input').each(function( index ) {
            let field = $(this)[0];

            if(!field.checkValidity()){
                let label = $(this).parent().find('.control-label').text();
                
                $.toaster({
                    priority : 'danger',
                    title : 'Type Error',
                    message : field.validationMessage+' '+label,
                    time : time
                });
            }
		});
	});
JS;

$this->registerJS($bloodhoundJS);
//$this->registerJS($toasterJS);