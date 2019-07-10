<?php

use common\models\Certificate;
use common\models\CertificateStatus;
use common\models\City;
use common\models\User;
use lajax\translatemanager\helpers\Language as Lx;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\ListView;
use yii\widgets\Pjax;

$this->title = Yii::t('titles', 'Certificates | Angdocs');

$certificateStatusOptions = CertificateStatus::getStatusOptions();
$certificateStatusCssOptions = CertificateStatus::getStatusOptions(true);
$certificateDateOptions = Certificate::getDateOptions();

function checkData($data){
    return $data? 'true':'false';
}

$pageSizeOptions = [
    10 => 'List 10',
    25 => 'List 25',
    50 => 'List 50',
    100 => 'List 100',
];

Pjax::begin([
    'enablePushState' => false,
    'enableReplaceState' => false,
    'timeout' => 60000,
]);

$form = ActiveForm::begin([
    'id' => 'filters-form',
    'action' => [''],
    'method' => 'get',
    'options' => [
        'data-pjax' => ''
    ]
]);

$pageSizeInput = Html::dropDownList('perPage', $filters['perPage'], $pageSizeOptions, [
    'class' => 'list-certificates form-control filter',
]);
?>

    <div class="mobile-title visible-xs"><?= Lx::t('frontend', 'certificates'); ?></div>
    
    <div class="container-fluid nopadding-mob">
        <div class="container nopadding-tab nopadding-mob">
            <div class="third-nav clearfix">
                <div class="hidden-xs col-sm-3 col-md-3">
                    <div class="group search-group">
                        <div class="input-wrapper">
                            <div class="relative">
                                <input type="text" name="search" id="search-filter" value="<?= $filters['search']; ?>">
                                <span class="search-icon">
                                    <i class="fa fa-search " aria-hidden="true"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="hidden-xs col-sm-3 col-md-3">
                    <div class="group search-group">
                        <div class="input-wrapper">
                            <select class="select-custom" id='select-city-filter' name="city">
                                <option value=""><?= Yii::t('frontend', 'Select a City') ?></option>
                                <?php
                                foreach (City::find()->all() as $key => $value) {
                                    $city = $value->getAttributes();
                                    $urlParams = Yii::$app->getRequest()->getQueryParams();
                                    $hasCityUrlParam = !empty($urlParams['city']);
                                    $selectedOption = $hasCityUrlParam && $urlParams['city'] == $city['id_city'];
                                    $selected = '';
                                    if ($selectedOption) {
                                        $selected = 'selected="selected"';
                                    }
                                    
                                    echo "<option value='{$city['id_city']}' {$selected}>{$city['name_'.Yii::$app->language]}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="hidden-xs col-sm-3 col-md-3">
                    <div class="group search-group">
                        <div class="input-wrapper">
                            <select class="select-custom" id='select-status-filter' name="status">
                                <option value=""><?= Yii::t('frontend', 'Select a Status') ?></option>
                                <?php
                                foreach (CertificateStatus::getStatusOptions() as $key => $value) {
                                    $urlParams = Yii::$app->getRequest()->getQueryParams();
                                    $hasStatusUrlParam = !empty($urlParams) && !empty($urlParams['status']) && is_numeric($urlParams['status']) and $urlParams['status'] > 0;
                                    $selectedOption = $hasStatusUrlParam && $urlParams['status'] == $key;
                                    $selected = '';
                                    if ($selectedOption) {
                                        $selected = 'selected="selected"';
                                    }
                                    
                                    echo "<option value='$key' {$selected}>{$value}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="hidden-xs col-sm-1 col-md-1">
                    <div class="group search-group">
                        <div class="input-wrapper">
                            <label class="control-label" for="reject"><?= Lx::t('frontend', 'Rejected?') ?></label>
                            <?php $urlParams = Yii::$app->getRequest()->getQueryParams();?>
                            <input type="checkbox" id="reject" name="reject" <?=isset($urlParams['reject']) && $urlParams['reject'] == 'on' ? 'checked="checked"':''?>/>
                        </div>
                    </div>
                </div>
                <?php
                /*
                <div class="col-xs-12 col-sm-5 col-md-5">
                <div class="show-activity--wrapper flex-center flex-direction--column">
                <div class="show-activity--title"><?= Lx::t('frontend', 'Show activity'); ?></div>
                <ul class="show-activity clearfix">
                <?php
                echo Html::hiddenInput('date', $filters['date'], ['id' => 'date-filter']);
                foreach ($certificateDateOptions as $date => $dateOption) {
                ?>
                <li <?= $filters['date'] == $date ? 'class="active"' : ''; ?>>
                    <a class="show-activity--btn" href="javascript: void(0);" data-date="<?= $date; ?>">
                        <?= $dateOption; ?>
                    </a>
                </li>
                <?php } ?>
                </ul>
                </div>
                </div>*/
                ?>
                <?php if (in_array(Yii::$app->user->identity->type, [User::TYPE_MANAGER, User::TYPE_CLIENT])) { ?>
                    <div class="hidden-xs hidden-sm col-md-5">
                        <a href="<?= Url::to("/certificates/create"); ?>" class="certificates-btn">
                            <?= Lx::t('frontend', 'new certificate'); ?>
                            <i class="fa fa-arrow-right" aria-hidden="true"></i>
                        </a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    
    <div class="container-fluid bg-home table-content--desktop hidden-xs">
        <?php
        $userType = Yii::$app->user->identity->type;

        $extraColumns = $userType == User::TYPE_MANAGER ? '<div class="cell title-cell text-uppercase">' . Lx::t('frontend',
                'Maker / Invoicer') . '</div>' : '';

        /*$downloadHSTemplate = $userType == User::TYPE_MANAGER || $userType == User::TYPE_MAKER ? '<a href="#" onclick="window.open('. " '/files/UploadHSProds_Template.xlsx','_blank' " .');" class="big-header--title">' . Lx::t('frontend',
        'HS Products Template') . '</a>': '';*/
        $downloadHSTemplate = '';
        
        if (!empty($certificatesProvider->models)) {
            try {
                echo ListView::widget([
                    'dataProvider' => $certificatesProvider,
                    'layout' => '<div class="table-wrapper">
<div class="big-header hidden-xs">
    <div class="header-text--padding flex-between width-full">
        <div class="big-header--title"> '. $downloadHSTemplate .' </div>
        <div class="select-wrapper display-flex">
            <div class="input-wrapper">
            ' . $pageSizeInput . '
            </div>
        </div>
    </div>
    <div class="btn-wrapper view">
        <a class="view-all--history-btn" href="' . Url::to('/site/index') . '">
            ' . Lx::t('frontend', 'reset filters') . '
            <i class="fa fa-arrow-right" aria-hidden="true"></i>
        </a>
    </div>
</div>
<div class="table">
    <div class="table-row header">
        <div class="cell title-cell text-uppercase">' . Lx::t('frontend', 'request id') . '</div>
        <div class="cell title-cell text-uppercase">' . Lx::t('frontend', 'company name') . '</div>
        <div class="cell title-cell text-uppercase">' . Lx::t('frontend', 'requester name') . '</div>
        <div class="cell title-cell text-uppercase">' . Lx::t('frontend', 'b/l') . '</div>
        <div class="cell title-cell text-uppercase">' . Lx::t('frontend', 'updated at') . '</div>
        <div class="cell title-cell text-uppercase">' . Lx::t('frontend', 'status') . '</div>
        <div class="cell title-cell text-uppercase"></div>
        <div class="cell title-cell text-uppercase">' . Lx::t('frontend', 'city') . '</div>
        ' . $extraColumns . '
        <div class="cell title-cell text-uppercase"></div>
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
                    'itemView' => function ($item) use ($certificateStatusOptions, $certificateStatusCssOptions) {
                        
                        return $this->renderAjax('/certificates/_item', [
                            'item' => $item,
                            'userStatusOptions' => $certificateStatusOptions,
                            'userStatusCssOptions' => $certificateStatusCssOptions
                        ]);
                    },
                ]);
            } catch (Exception $e) {
                echo 'An error has occurred in List loading the widget.';
            }
        } else {
            ?>
            <div class="col-md-12 bg-white">
                <div class="table-wrapper">
                    <div class="big-header hidden-xs">
                        <div class="header-text--padding flex-between width-full">
                            <div class="big-header--title"></div>
                            <div class="select-wrapper display-flex">
                                <div class="select-label-wrapper">
                                    <label>&nbsp;</label>
                                    <?= $pageSizeInput; ?>
                                </div>
                            </div>
                        </div>
                        <div class="btn-wrapper view">
                            <a class="view-all--history-btn" href="<?= Url::to('/site/index'); ?>">
                                <?= Lx::t('frontend', 'reset filters'); ?>
                                <i class="fa fa-arrow-right" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                    <div class="table">
                        <div class="table-row header">
                            <div class="cell title-cell text-uppercase"><?= Lx::t('frontend', 'request id'); ?></div>
                            <div class="cell title-cell text-uppercase"><?= Lx::t('frontend', 'company name'); ?></div>
                            <div class="cell title-cell text-uppercase"><?= Lx::t('frontend',
                                    'requester name'); ?></div>
                            <div class="cell title-cell text-uppercase"><?= Lx::t('frontend', 'b/l'); ?></div>
                            <div class="cell title-cell text-uppercase"><?= Lx::t('frontend', 'updated at'); ?></div>
                            <div class="cell title-cell text-uppercase"><?= Lx::t('frontend', 'status'); ?></div>
                            <div class="cell title-cell text-uppercase"></div>
                            <div class="cell title-cell text-uppercase"><?= Lx::t('frontend', 'city'); ?></div>
                            <?= $extraColumns; ?>
                            <div class="cell title-cell text-uppercase"></div>
                        </div>
                    </div>
                    <div class="no-results text-center">
                        <?= 'There are no results that match your search.'; ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
    
    <div class="table-content--mobile visible-xs">
        <?php
        try {
            echo ListView::widget([
                'dataProvider' => $certificatesProvider,
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
                    return $this->render('/certificates/_item', [
                        'item' => $item,
                        'userStatusOptions' => $certificateStatusOptions,
                        'userStatusCssOptions' => $certificateStatusCssOptions,
                        'mobile' => true
                    ]);
                },
            ]);
        } catch (Exception $e) {
            echo 'An error has occurred in List loading the widget.';
        }
        ?>
    </div>

<?php
echo $this->render('/certificates/_modal');

ActiveForm::end();
Pjax::end();

$formInvoice = ActiveForm::begin([
    'id' => 'invoice-form',
    'action' => [''],
    'method' => 'post',
    'options' => [
        'enctype' => 'multipart/form-data',
        'class' => 'hidden',
    ]
]);


echo '<div class="cecrtificate-data-invoice" style="display: none;">...</div>';


echo Html::hiddenInput('type-form-upload', 'invoice', ['id' => 'type-form-upload']); // invoice or certificate
echo Html::hiddenInput('id-user-client', 1, ['id' => 'id-user-client']);

$labelPrice = Lx::t('frontend', 'Price');
echo '<div class="invoice-price">' . ' <div  id="price-label" class="input-label description">'.$labelPrice.'</div>';
echo Html::textInput('invoice-price', '', [
    'id' => 'invoice-price',
    'class' => 'form-control',
    'type' => 'number'
]);
echo "</div>";

$labelDate = Lx::t('frontend', 'Date');
echo '<div class="invoice-date">' . ' <div class="input-label description">'.$labelDate.'</div>';
echo Html::textInput('invoice-date', '', [
    'id' => 'invoice-date',
    'class' => 'form-control',
    'type' => 'text',
    'data-date-form' => 'YYYY-MM-DD',
]);
echo "</div>";

$labelNumber = Lx::t('frontend', 'Number');
echo '<div class="invoice-nr">' . ' <div class="input-label description">'.$labelNumber.'</div>';
echo Html::textInput('invoice-nr', '', [
    'id' => 'invoice-nr',
    'class' => 'form-control',
    'type' => 'text'
]);
echo "</div>";

echo Html::textInput('id-certificates', null, ['id' => 'id-certificates']);
echo Html::fileInput('invoice-file-ajax', '', [
    'id' => 'invoice-file-ajax',
    'class' => 'form-control hidden',
    'type' => 'file'
]);

ActiveForm::end();

$formDraft = ActiveForm::begin([
    'id' => 'draft-form',
    'action' => [''],
    'method' => 'post',
    'options' => [
        'enctype' => 'multipart/form-data',
        'class' => 'hidden',
    ]
]);
echo Html::hiddenInput('type-form-upload', 'draft', ['id' => 'type-form-upload']);
echo Html::hiddenInput('id-user-client', 1, ['id' => 'id-user-client']);

echo Html::textInput('id-certificates', null, ['id' => 'id-certificates']);
echo Html::fileInput('invoice-file-ajax', '', [
    'id' => 'invoice-file-ajax',
    'class' => 'form-control hidden',
    'type' => 'file'
]);
ActiveForm::end();

$formDraftValidation = ActiveForm::begin([
    'id' => 'validation-form',
    'action' => [''],
    'method' => 'post',
    'options' => [
        'enctype' => 'multipart/form-data',
        'class' => 'hidden',
    ]
]);
echo Html::hiddenInput('type-form-upload', 'draft', ['id' => 'type-form-upload']);
echo Html::hiddenInput('id-user-client', 1, ['id' => 'id-user-client']);
echo Html::fileInput('draft-validation-file-ajax[]', '', [
    'id' => 'draft-validation-file-ajax',
    'class' => 'form-control hidden',
    'type' => 'file',
    'multiple' => true
]);

ActiveForm::end();

$formValidatedFile = ActiveForm::begin([
    'id' => 'validated-form',
    'action' => [''],
    'method' => 'post',
    'options' => [
        'enctype' => 'multipart/form-data',
        'class' => 'hidden',
    ]
]);
echo Html::hiddenInput('type-form-upload', 'draft', ['id' => 'type-form-upload']);
echo Html::hiddenInput('id-user-client', 1, ['id' => 'id-user-client']);
echo Html::fileInput('validated-file-ajax', '', [
    'id' => 'validated-file-ajax',
    'class' => 'form-control hidden',
    'type' => 'file',
    'multiple' => true
]);

echo '<div class="draft-fields">' . ' <div class="input-label description">Arc Number</div>';
echo Html::textInput('draft-nr', '', [
    'id' => 'draft-nr',
    'class' => 'form-control',
    'type' => 'number'
]);
echo '<div class="input-label description">Arc Value</div>';
echo Html::textInput('draft-value', '', [
    'id' => 'draft-value',
    'class' => 'form-control',
    'type' => 'text'
]);
echo "</div>";

ActiveForm::end();

$formProvePaymentFile = ActiveForm::begin([
    'id' => 'prove-payment-form',
    'action' => [''],
    'method' => 'post',
    'options' => [
        'enctype' => 'multipart/form-data',
        'class' => 'hidden',
    ]
]);
echo Html::hiddenInput('type-form-upload', 'prove-payment', ['id' => 'type-form-upload']);
echo Html::fileInput('prove-file-ajax', '', [
    'id' => 'prove-file-ajax',
    'class' => 'form-control hidden',
    'type' => 'file'
]);

$labelOther = Lx::t('frontend', 'Check');
echo '<div class="proof-rules-price row content">';
echo '<div class="col-lg-12 proof-number">';
echo '<div class="input-label description" id="proof-nr">'. $labelOther .'</div>';
echo Html::textInput('proof-value', '', [
    'id' => 'proof-value',
    'class' => 'form-control'
]);
echo '</div>';
echo '<div class="col-md-6">';
$labelOtherPaymentMethod = Lx::t('frontend', 'Search other payment Method');
echo ' <div class="input-label description">'. $labelOtherPaymentMethod .'</div>';
echo Html::checkbox('other-payment', '', [
    'id' => 'other-payment'
]);
echo '</div>';
$labelPaymentMethod = Lx::t('frontend', 'Payment Method');
echo '<div class="proof-payment-method col-md-6">';
echo ' <div class="description">'. $labelPaymentMethod .'</div>';
echo Html::dropDownList('payment-method', null,Certificate::getPaymentMethodOptions(),['id'=>'payment-method']);
echo '</div></div>';

ActiveForm::end();


$base = Url::base(true);

$urlCertificateInformation = $base.'/site/get-certificate-informations';
$urlInvoiceInformation = $base.'/site/get-invoice-informations';
$urlDocuments = $base.'/site/get-documents';

$quantityCertificate = Yii::t('frontend', 'Quantity of Ceritificates');
$sumContainers = Yii::t('frontend', 'Sum of container loads');

$certificatesSelected = Yii::t('frontend', 'Certificates Selected: ');
$filtersJs = <<<JS
$(document).on('ready pjax:success', function () {
    $(".filter").change(function () {
        $("#filters-form").submit();
    });

    $("#search-filter").keypress(function (e) {
        if (e.which === 13) {
            $("#filters-form").submit();
        }
    });

    $(".show-activity li a").click(function () {
        var thisElem = $(this);

        $(".show-activity li").removeClass('active');
        thisElem.parent().addClass('active');

        $("#date-filter").val(thisElem.attr('data-date'));

        $("#filters-form").submit();
    });

    $("#select-city-filter").change(function () {
        var thisElem = $(this);

        $("#filters-form").submit();
    });

    $("#reject").click(function () {
        $("#filters-form").submit();
    });

    

    $("#select-status-filter").change(function () {
        var thisElem = $(this);

        $("#filters-form").submit();
    });

    $('body').on('click', '.change-status--btn', function () {
    //$(".change-status--btn").click(function () {
        var thisElem = $(this);

        $("#id-certificate").val(thisElem.data('id'));
        certificate_id = $(this).data('id');

        $(".upload-group").addClass("hidden");
        
        if (thisElem.attr('data-validate') === "1" || thisElem.attr('data-draft-validation') === 'true') {
            $(".change-status--btn-yes").css('display', 'none');
            $(".validate-btn").css('display', 'block');

            if (thisElem.attr('data-validate-list') === "1") {
                $(".validate-group").show();
                $(".price-group").show();
                $(".description-group").hide();
                $(".reject-btn").hide();
            }
        } else {
            $(".validate-group").hide();
            $(".price-group").hide();
            $(".description-group").show();
        }

        if (thisElem.data("invoice") == true) {
        	
            $("#type-form-upload").val("invoice");
            client_id = $(this).data('client-id');
            certificate_id = $(this).data('id');
            $("#id-certificates").val(certificate_id);

            var str = "";
            var selectedCertificates = new Array();
            $(".upload-group").removeClass("hidden");
            $(".description-group").addClass("hidden");

            $('input.invoice:checked([data-client-id="' + client_id + '"])').each(function (index) {
                selectedCertificates.push($(this).data("id"));
            });
            if ($.inArray(certificate_id, selectedCertificates) == -1) {
                selectedCertificates.push(certificate_id);
            }

            str = selectedCertificates.length + " $certificatesSelected" + " " + selectedCertificates.join(",");
            $("#id-certificates").val(selectedCertificates.join(","));
            $(".multi-certificates").html(str);

            // Certificate Data in invoice
            $(".cecrtificate-data-invoice").prependTo($(".upload-group"));
            $(".cecrtificate-data-invoice").show();
            
            
            function getInvoiceInformations(invoiceNumber) {
                $.ajax({
	                dataType: "json",
	                url: '$urlInvoiceInformation',
	                data: {invoiceNumber: invoiceNumber},
	                success: function(data){
	                	let formatMoney = new Intl.NumberFormat('an-AN', {
						  style: 'currency',
						  currency: 'EUR'
						});
	                	let formatNumber = new Intl.NumberFormat('an-AN');
	                	let information = '';
	                	let hasPackageNr = $.isNumeric(data.lading_packages_nr) && data.lading_packages_nr>0;
	                	let hasVolume = $.isNumeric(data.lading_volume) && data.lading_volume>0;
	                	let hasWeight = $.isNumeric(data.lading_weight) && data.lading_weight>0;
	                	let hasMessage = typeof data.message != 'undefined';
	                	
	                	$(".cecrtificate-data-invoice").html('<img src="/themes/images/load.gif" width="16" />');
	                	
	                	if((hasPackageNr || hasVolume || hasWeight) && !hasMessage){
		                    let certificateLanfingInformation = "<p class='text-muted'><b>$quantityCertificate:</b> "+formatNumber.format(data.lading_packages_nr)+"</p>" +
		                    "<p class='text-muted'><b>$sumContainers:</b> "+formatNumber.format(data.lading_weight)+"</p>" +
		                    "<p class='text-muted'><b>m³:</b> "+formatNumber.format(data.lading_volume)+"</p>";
		                    
		                    information = certificateLanfingInformation;
	                    }
	                    
	                    if(hasMessage)
	                		information = 'New Certificate';
	                	
	                	setTimeout(function() {
						    $(".cecrtificate-data-invoice").html(information);
						}, 500);
	                }
                });
            }
           
            function getCertificateInformations() {
                $.ajax({
	                dataType: "json",
	                url: '$urlCertificateInformation',
	                data: {certificates: window.certificateToInvoceList},
	                success: function(data){
	                	let formatMoney = new Intl.NumberFormat('an-AN', {
						  style: 'currency',
						  currency: 'EUR'
						});
	                	let formatNumber = new Intl.NumberFormat('an-AN');
	                	let information = '';
	                	let hasPackageNr = $.isNumeric(data.lading_packages_nr) && data.lading_packages_nr>0;
	                	let hasVolume = $.isNumeric(data.lading_volume) && data.lading_volume>0;
	                	let hasWeight = $.isNumeric(data.lading_weight) && data.lading_weight>0;
	                	let hasMessage = typeof data.message != 'undefined';
	                	
	                	$(".cecrtificate-data-invoice").html('<img src="/themes/images/load.gif" width="16" />');
	                	
	                	if((hasPackageNr || hasVolume || hasWeight) && !hasMessage){
		                    let certificateLanfingInformation = "<p class='text-muted'><b>$quantityCertificate:</b> "+formatNumber.format(data.lading_packages_nr)+"</p>" +
		                    "<p class='text-muted'><b>$sumContainers:</b> "+formatNumber.format(data.lading_weight)+"</p>" +
		                    "<p class='text-muted'><b>m³:</b> "+formatNumber.format(data.lading_volume)+"</p>";
		                    
		                    information = certificateLanfingInformation;
	                    }
	                    
	                    if(hasMessage)
	                		information = 'New Certificate';
	                	
	                	setTimeout(function() {
						    $(".cecrtificate-data-invoice").html(information);
						}, 500);
	                }
                });
            }
            
            //Get Certifications
            getCertificateInformations();
            
            
            $(".upload-group").removeClass("hidden");
            $("#invoice-file-ajax").appendTo($(".input-upload"));
            $("#invoice-file-ajax").removeClass("hidden");

            $(".multi-certificates").removeClass("hidden");
            $(".invoice-price").appendTo($(".input-upload"));
            $(".invoice-rules-price").appendTo($(".input-upload"));
            $(".invoice-date").appendTo($(".input-upload"));
            $("#invoice-date").datetimepicker({
                format: 'YYYY-MM-DD',
            });
            $(".invoice-nr").appendTo($(".input-upload"));
            $("#id-user-client").val(client_id);
        } else {
            $(".multi-certificates").addClass("hidden");
            $(".cecrtificate-data-invoice").hide();
            $(".description-group").removeClass("hidden");
        }

        if (thisElem.data("certificate-official") == true) {
            $("#type-form-upload").val("certificate");
            client_id = $(this).data('client-id');
            certificate_id = $(this).data('id');
            $("#invoice-file-ajax").appendTo($(".input-upload"));
            $("#invoice-file-ajax").removeClass("hidden");
            $(".upload-group").removeClass("hidden"); $("#id-certificates").val(certificate_id);

            $("#id-user-client").val(client_id);
        } else {
            $(".multi-certificates").addClass("hidden");
        }

        if (thisElem.data("siga-draft-file") == true) {
        	$(".description-group").hide();

            //Show fields
            $(".draft-group").show();
            $("#siga-file-ajax").remove();
            $("#invoice-file-ajax").clone().prop('id', 'siga-file-ajax').prop('name', 'siga-file-ajax').appendTo($(".draft-upload")).removeClass("hidden");
            
            //implements
            $("#type-form-upload").val("siga-draft-file");
            client_id = $(this).data('client-id');
            certificate_id = $(this).data('id');

            $("#id-certificate").val(certificate_id);
            $("#id-user-client").val(client_id);

        } else {
            $(".draft-group").hide();
            $(".description-group").show();
        }

        if (thisElem.data("draft-validation") == true) {
            $("#type-form-upload").val("draft-validation");
            $(".validate-draft-validation-group").show();
            $(".description-group").show();
            $("#draft-validation-file-ajax").appendTo($(".draft-validation-upload"));
            $("#draft-validation-file-ajax").removeClass("hidden");
            $(".accept-btn").show();
            $(".reject-btn").hide();
            
            let validatedOpt = $('input[name=validateDraftValidationList]').first();
            validatedOpt.prop("checked", true);
            $(".draft-validation-group").hide();

            certificate_id = $(this).data('id');
            $("#id-certificate").val(certificate_id);
        }else{
        	$(".validate-draft-validation-group").hide();
        	$(".draft-validation-group").hide();
        }

        if (thisElem.data("validated-file") == true) {
            $("#type-form-upload").val("validated-file");
            $(".description-group").hide();
            $(".validated-file-group").removeClass('hidden');
            $("#validated-file-ajax").appendTo($(".validated-upload"));
            $("#validated-file-ajax").removeClass("hidden");
            
            $(".draft-fields").appendTo($(".validated-upload"));

            certificate_id = $(this).data('id');
            $("#id-certificate").val(certificate_id);
        } else {
            $(".validated-file-group").addClass('hidden');
            $("#validated-file-ajax").addClass("hidden");
        }

        if (thisElem.data("prove-payment") == true) {
            $("#type-form-upload").val("prove-payment");
            $(".description-group").hide();
            $(".prove-file-group").removeClass('hidden');
            $("#prove-file-ajax").appendTo($(".prove-upload"));
            $("#prove-file-ajax").removeClass("hidden");

            $(".proof-rules-price").appendTo($(".prove-upload"));
            
            hideShowPaymentMethod(false);
            function hideShowPaymentMethod(option = true) {
                $('.proof-payment-method').hide();
                $('.proof-number').hide();
                $('#payment-method').val(0);
                $('#proof-value').val('');
                
            	if(option){
            		$('.proof-payment-method').show();
                    $('.proof-number').show();
                }
            }
            
            $('#other-payment').change(function() {
                 hideShowPaymentMethod(this.checked) ;
            });
            
            $('#payment-method').change(function() {
            	let paymentMethod = $('#payment-method').val();
            	let textSelect = $('#payment-method').find('option:selected').text();
            	
            	if(paymentMethod >= 1){
            	    $('#proof-nr').html(textSelect);
                }else{
            		$('#invoice-price').prop('type', 'number');
                }
            });

        } else {
            $(".prove-file-group").addClass('hidden');
            $("#prove-file-ajax").addClass("hidden");
        }

        if (thisElem.data("to-print") == true) {
        	$("#type-form-upload").val("to-print");

            getDocuments();

            function getDocuments() {
                $.ajax({
	                dataType: "json",
	                url: '$urlDocuments',
	                data: {certificateID: certificate_id},
	                success: function(data){
	                	let filesDownload = '';
                        $.each(data.certificate, function(index,element){
                            filesDownload += "<tr>" +
                             "<td><input type='checkbox' name='file["+element.id_doc+"]' data-id='"+element.id_doc+"' data-name='"+element.name+"' data-path='"+element.path+"' id='file-"+element.id_doc+"' class='files-download' /></td> " +
                             "<td>"+index+": "+element.name+"</td>" +
                              "</tr>";
                            //<button class='btn link-download' data-path='"+element.path+"' ></button>
                            /*
	                            $('.link-download').click(function() {
					                alert('teste');
					             });
                            */
                        });
                        
                        $('.table-file-download').remove();
                        
                        $( ".description-group" ).append('<div class="table-file-download"><br/>' +
                         '<table class="table">' +
                          '<tr>' +
                           '<th>#</th>' +
                           '<th>Files to Download</th>' +
                          '</tr>'+filesDownload+'</table></div>');
	                }
                });
            }
        } else {
        	$(".table-file-download").hide();
        }
        
        if (thisElem.data("validate-list") == true) {
        	$('input[name=validateList]').first().prop("checked", true);
        }
        
    });

    $("#validate-draft-validation-list").change(function () {
        var selectedValue = $('input:radio[name=validateDraftValidationList]:checked').val();
        
        if (selectedValue === "1") {
            $(".draft-validation-group").show();
            $(".accept-btn").hide();
            $(".reject-btn").show();
        } else {
            $(".draft-validation-group").hide();
            $(".reject-btn").hide();
            $(".accept-btn").show();
        }
    });

    $("#validate-list").change(function () {
        var selectedValue = $('input:radio[name=validateList]:checked').val();
        if (selectedValue === "1") {
            $(".price-group").hide();
            $(".accept-btn").hide();
            $(".reject-btn").show();
        } else {
            $(".price-group").show();
            $(".reject-btn").hide();
            $(".accept-btn").show();
        }
    });

    $(".change-status--btn-yes").click(function () {

        var isUploadGroupHide = $(".upload-group").hasClass("hidden") == true;
        var isDraftGroupHide = $(".draft-group").hasClass("hidden") == true;
        var isValidatedGroupHide = $(".validated-file-group").hasClass("hidden") == true;
        var isProveGroupHide = $(".prove-file-group").hasClass("hidden") == true;

        var isTypeFormInvoice = $('#type-form-upload').val() == 'invoice' && !isUploadGroupHide;
        var isTypeFormDraftSignedFile = $('#type-form-upload').val() == 'siga-draft-file' && !isDraftGroupHide;
        var isTypeFormValidatedFile = $('#type-form-upload').val() == 'validated-file' && !isValidatedGroupHide;
        var isTypeFormProveFile = $('#type-form-upload').val() == 'prove-payment' && !isProveGroupHide;
        var isTypeFormToPrint =  $('#type-form-upload').val() == 'to-print';

        $(this).attr('disabled', 'disabled');
        $("#change-status").val(1);

        if (isTypeFormInvoice) {
            $("#invoice-nr").appendTo($("#invoice-form"));
            $("#invoice-date").appendTo($("#invoice-form"));
            $("#invoice-price").appendTo($("#invoice-form"));
            $("#invoice-file-ajax").addClass("hidden");
            $("#invoice-file-ajax").appendTo($("#invoice-form"));
            $("#invoice-description").val($("#validate-description").val());
            $("#type-form-upload").appendTo($("#invoice-form"));
            if ($("#invoice-file-ajax").val() != '') {
                $("#invoice-form").submit();
            }
        }

        if (isTypeFormDraftSignedFile) {
            $("#id-certificate").appendTo($("#draft-form"));
            $("#siga-file-ajax").addClass("hidden");
            $("#siga-file-ajax").appendTo($("#draft-form"));
            $("#type-form-upload").appendTo($("#draft-form"));

            $("#draft-form").submit();
        }

        if (isTypeFormValidatedFile) {
            $("#draft-nr").appendTo($("#validated-form"));
            $("#draft-value").appendTo($("#validated-form"));
            $("#id-certificate").appendTo($("#validated-form"));
            $("#validated-file-ajax").addClass("hidden");
            $("#validated-file-ajax").appendTo($("#validated-form"));
            $("#type-form-upload").appendTo($("#validated-form"));

            $("#validated-form").submit();
        }

        if (isTypeFormProveFile) {
            $("#id-certificate").appendTo($("#prove-payment-form"));
            $("#prove-file-ajax").appendTo($("#prove-payment-form"));
            $("#validate-description").appendTo($("#prove-payment-form"));
            $("#type-form-upload").appendTo($("#prove-payment-form"));
            
            $("#other-payment").appendTo($("#prove-payment-form"));
            $("#payment-method").appendTo($("#prove-payment-form"));
            $("#proof-value").appendTo($("#prove-payment-form"));
            

            $("#prove-file-ajax").addClass("hidden");

            $("#prove-payment-form").submit();
        }
        
        if(isTypeFormToPrint){
        	
        	$('.files-download').each(function(index,element) {
        		let id = '';
        		let name = '';
        		let path = '';
        		
        		if(this.checked){
        		    let id = $(element).data('id');
        			let name = $(element).data('name');
        			let path = $(element).data('path');
        			
        			let fileData = path.split('.');
        			let extension = fileData[fileData.length-1];
        			
        		    newWin = window.open(path,'_blank');
        		    if($.inArray( extension, [ 'jpeg','jpg','gif','png','bmp','tiff' ] ) !== -1){
        		        newWin.document.write('<img src="$base/'+path+'" alt="Smiley face" width="100%"/>');//divToPrint.outerHTML
        		        newWin.print();
        		        newWin.close();
        		    }
        		}
        	});
        	
        	$("#filters-form").submit();
            
            //location.reload();
        }


        if (!(isTypeFormInvoice || isTypeFormDraftSignedFile || isTypeFormValidatedFile || isTypeFormProveFile || isTypeFormToPrint)) {
        	
        	
            $("#filters-form").submit();
        }

        $("#change-status--modal").modal('hide');
    });

    $(".reject-btn").click(function () {
        var isDraftValidationGroupHide = $(".draft-validation-group").hasClass("hidden") == true;
        var isTypeFormDraftValidation = $('#type-form-upload').val() == 'draft-validation' && !isDraftValidationGroupHide;

        if (isTypeFormDraftValidation) {
            $("#id-certificate").appendTo($("#validation-form"));
            $("#draft-validation-file-ajax").addClass("hidden");
            $("#draft-validation-file-ajax").appendTo($("#validation-form"));
            $("#type-form-upload").appendTo($("#validation-form"));
            $("#validate").val(1);
            $("#validated").val(0);
            $("#change-status").val(1);
            $("#validate").appendTo($("#validation-form"));
            $("#validated").appendTo($("#validation-form"));
            $("#change-status").appendTo($("#validation-form"));

            $("#validation-form").submit();
        } else {
            $(this).attr('disabled', 'disabled');
            $("#validate").val(1);
            $("#validated").val(0);
            $("#change-status").val(1);
            $("#filters-form").submit();
            $("#change-status--modal").modal('hide');
        }
    });

    $(".accept-btn").click(function () {
        $(this).attr('disabled', 'disabled');
        $("#validate").val(1);
        $("#validated").val(1);
        $("#change-status").val(1);
        var isDraftValidation = $("#type-form-upload").val() == 'draft-validation';
        var hasUploadGroup = !$(".upload-group").hasClass("hidden") == true;


        if (isDraftValidation) {
            var description = $("#validate-description");
            var idCertificate = $("#id-certificate");
            var mainForm = $("#draft-form");

            $(description).appendTo(mainForm);
            $(idCertificate).appendTo(mainForm);

            mainForm.submit();
        }


        if (!hasUploadGroup && !isDraftValidation) {
            $("#filters-form").submit();
        }

        if (hasUploadGroup && !isDraftValidation) {
            $("#invoice-file-ajax").addClass("hidden");
            $("#invoice-file-ajax").appendTo($("#invoice-form"));
            $("#invoice-description").val($("#validate-description").val());
            if ($("#invoice-file-ajax").val() != '') {
                $("#invoice-form").submit();
            }
        }

        $("#change-status--modal").modal('hide');
    });

    $("#change-status--modal").on('hidden.bs.modal', function () {
        $("#change-status").val(0);
        $("#id-certificate").val(null);
        $("#validate").val(0);
        $("#validated").val(0);
        $(".validate-btn").css('display', 'none');
        $(".change-status--btn-yes").css('display', 'block');
    });
});
JS;

$this->registerJs($filtersJs, $this::POS_READY);

$invoiceJS = <<<JS
$(document).on('change', 'input.invoice', function(){
	client_id =  $(this).data('client-id');
	if($(this).prop('checked')==true){
		/* disabled ALL other companies inputs */
        $('input.invoice:not([data-client-id="'+client_id+'"])').prop('disabled', true);
    } else {
        /* only enable other companies inputs if there are none :selected for present company */
        if($('input.invoice:checked([data-client-id="'+client_id+'"])').length == 0){
            $('input.invoice:not([data-client-id="'+client_id+'"])').prop('disabled', false);
        }
    }
});
JS;
$this->registerJs($invoiceJS, $this::POS_READY);

//Certificate
$certificateSumToInvoiceJS = <<<JS
window.certificateToInvoceList = [];

$("body").on('change','.invoice', function() {
    let idcertificate = $(this).data('id');
    
    if($.isNumeric(idcertificate)){
        if(this.checked){
            window.certificateToInvoceList.push(idcertificate);
        }else{
            window.certificateToInvoceList.splice(window.certificateToInvoceList.indexOf(idcertificate),1);
        }
    }
});
JS;
$this->registerJs($certificateSumToInvoiceJS, $this::POS_READY);

