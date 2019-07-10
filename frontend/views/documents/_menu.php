<?php

use lajax\translatemanager\helpers\Language as Lx;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use common\models\base\Certificate as BaseCertificate;

$action = Yii::$app->controller->action->id;

$get = \Yii::$app->request->isGet ? \Yii::$app->request->get():null;
$filter = !empty($get['search_document'])? $get['search_document']: null;


$hasPaymentMethod = isset($get['payment_method']) && (!empty($get['payment_method']) || $get['payment_method'] === '0');
$hasOtherPaymentProof = isset($get['other_payment_proof']) && !empty($get['other_payment_proof']);

$paymentMethod = $hasPaymentMethod ? $get['payment_method']: null;
$otherPaymentProof = $hasOtherPaymentProof ? $get['other_payment_proof']: null;

?>

<div class="mobile-title visible-xs"><?= Lx::t('frontend', 'Documents'); ?></div>

<div class="container-fluid">
    <div class="container">
	    <?php
	        $form = ActiveForm::begin([
	            'id' => 'filters-form-document',
	            'action' => [''],
	            'method' => 'get',
	            'options' => [
	                'data-pjax' => ''
	            ]
	        ]);
        ?>
        <div class="third-nav clearfix">
            <div class="col-md-6">
                <div class="flex-it align-items--center small-title--group">
                    <ul class="small-menu--certificates docs clearfix">
                        <li <?= $action == 'certificates' ? 'class="active"' : ''; ?>>
                            <a href="<?= Url::to("/documents/certificates"); ?>"><?= Lx::t('frontend', 'Certificates'); ?></a>
                        </li>
                        <li <?= $action == 'invoices' ? 'class="active"' : ''; ?>>
                            <a href="<?= Url::to("/documents/invoices"); ?>"><?= Lx::t('frontend', 'Invoices'); ?></a>
                        </li>
                    </ul>
                </div>
            </div>
			<?php /*
	        <div class="col-md-2">
				<label><?= Lx::t('frontend', 'Payment Number'); ?></label>
                <?=Html::input('text','other_payment_proof',$otherPaymentProof,['id'=>'other_payment_proof'])?>
	        </div>
			*/?>

	        <div class="col-md-2">
				<label><?= Lx::t('frontend', 'Other Payment Method'); ?></label><br/>
                <?=Html::dropDownList('payment_method',$paymentMethod, array_merge([''=>Yii::t('frontend','All')],BaseCertificate::getPaymentMethodOptions()),['id'=>'payment_method'])?>
	        </div>
	        <div class="col-md-4">
		        <div class="group search-group">
					<div class="input-wrapper">
						<label><?= Lx::t('frontend', 'Search Certificates'); ?></label>
						<div class="relative">
							<input type="text" name="search_document" id="search-filter-document" value="<?=$filter;?>">
							<span class="search-icon">
								<i class="fa fa-search" id="submit-documento-search" aria-hidden="true"></i>
							</span>
						</div>
					</div>
		        </div>
	        </div>
        </div>
	    <?php ActiveForm::end();?>
    </div>
</div>
<script>
	$(document).on('keypress',function(e) {
		if(e.which == 13)
			filterInvoices(true);
	});

	$('#submit-documento-search').click(function(){
		filterInvoices();
	});
	$('#payment_method').change(function(){
		filterInvoices(true);
	});
	$('#other_payment_proof').keyup(function(){
		filterInvoices();
	});
	
	function filterInvoices(autoSubmit = false){
		let hasOtherPaymentProof = $('#other_payment_proof').val() != '';
		let hasOtherPaymentMethod = $('#payment_method').val() != '';
		let hasSearchFilterDocument = $('#search-filter-document').val() != '';

		if(hasOtherPaymentProof || hasOtherPaymentMethod || hasSearchFilterDocument || autoSubmit) {
			$('#filters-form-document').submit()
		}
	}
</script>