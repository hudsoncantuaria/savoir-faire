<?php

use lajax\translatemanager\helpers\Language as Lx;
use yii\helpers\Url;
use common\models\Certificate;

// get invoice certificates association
$invoiceCertificates = $item->invoicesCertificates;

if (empty($mobile)) {
    ?>
	<div class="cell data-cell date"><?= $item->created; ?></div>
	<div class="cell data-cell certificate"><?= $item->nr; ?></div>
	<div class="cell data-cell docs-status status">€ <?=number_format($item->price,2)?></div>
	
	<?php
        $certificateEntity = new Certificate();
        foreach ($invoiceCertificates as $invoiceCertificate){
            $certificate = $certificateEntity->find()->where('id_certificate=:certificate',[':certificate'=>$invoiceCertificate->id_certificate])->one();
            if(!empty($certificate->docsProofOfPayment->path) and !empty($certificate->other_payment_proof)){
                break;
            }elseif(!empty($certificate->docsProofOfPayment->path)){
                break;
            }
        } ?>
	<div class="cell data-cell payment-informations"><?php
		$hasOtherPayment = !empty($certificate->other_payment_proof) && $certificate->other_payment_proof != 0;
		if($hasOtherPayment){
            echo $certificate->other_payment_proof.'<br/>';
            echo Certificate::getPaymentMethodOptions()[$certificate->payment_method];
		}
		
		?></div>
	<div class="cell data-cell download-btn--wrapper">
        <?php
        
        if(isset($certificate->docsProofOfPayment->path) && !empty($certificate->docsProofOfPayment->path)){?>
			<br/>
			<a class="download-btn gold" target="_blank" href="/<?= $certificate->docsProofOfPayment->path; ?>" data-pjax="0">
				<small><?= Lx::t('frontend', 'Proof of payment'); ?> <i class="fa fa-download" aria-hidden="true"></i></small>
			</a>
            <?php
        }?>
		
		<a class="download-btn" target="_blank" href="/<?= $item->doc->path; ?>" data-pjax="0">
            <?= Lx::t('frontend', 'download'); ?><i class="fa fa-download" aria-hidden="true"></i>
		</a>
		<i class="fa fa-search green icon-modal" aria-hidden="true" data-toggle="modal" data-target="#invoice-certificates-modal-<?= $item->primaryKey; ?>"></i>
	</div>
<?php } else { ?>
	<a href="/<?= $item->doc->path; ?>" target="_blank" data-pjax="0">
		<div class="col-xs-8">
			<div class="certificate-date"><?= $item->created; ?></div>
			<br>
			<div class="certificate-name"><?= $item->nr ; ?></div>
		</div>
		<div class="col-xs-4">
			<b>€ <?=number_format($item->price,2)?></b>
		</div>
		<i class="fa fa-file-o green" aria-hidden="true" data-toggle="modal" data-target="#invoice-certificates-modal"></i>
	</a>
<?php } ?>
<div id="invoice-certificates-modal-<?= $item->primaryKey; ?>" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title"><?= Lx::t('frontend', 'This Invoice is associated with these Certificates'); ?></h4>
			</div>
			<div class="modal-body">
				<ul class="certificates-included text-center clearfix">
                    <?php
                    foreach ($invoiceCertificates as $invoiceCertificate) {
                        $certificate = $invoiceCertificate->certificate;
                        ?>
						<li>
							<a href="<?= Url::to("/certificates/view?id={$certificate->primaryKey}"); ?>">
                                <?= Lx::t('frontend', 'Certificate'). ' '. $certificate->primaryKey; ?>
							</a>
						</li>
                    <?php } ?>
				</ul>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal"><?= Lx::t('frontend', 'Close'); ?></button>
			</div>
		</div>
	</div>
</div>
