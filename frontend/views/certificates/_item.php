<?php

use common\models\CertificateStatus;
use common\models\User;
use lajax\translatemanager\helpers\Language as Lx;
use yii\helpers\Url;

$userType = Yii::$app->user->identity->type;
$userId = Yii::$app->user->identity->id;

$changeStatusTitle = 'Change Status';
$canValidate = $canValidateList = 0;
$canChange = $multiInvoice = $uploadCertificateOfficial = $sigaDraftFile = $DraftValidation = $validatedFile = $proveFile = $toPrint = false;

if (in_array($userType, [User::TYPE_CLIENT, User::TYPE_MANAGER]) ){
	
	$hasDocDraftRequest = is_numeric($item->id_docs_draft_request_signed) and $item->id_docs_draft_request_signed > 0;
	$isSubmit = in_array($item->last_status, [CertificateStatus::STATUS_CREATED, null]) && $hasDocDraftRequest;
	$isValidation = in_array($item->last_status, [CertificateStatus::STATUS_DRAFT_VALIDATION]) && !$isSubmit;
	$canProvePayment = $item->last_status == CertificateStatus::STATUS_INVOICE;
	
	if ($isSubmit)
		$changeStatusTitle = 'Submit';
	
	if ($isValidation){
		$changeStatusTitle = 'Validate / Reject';
		$DraftValidation = true;
	}
    
    if ($canProvePayment) {
        $changeStatusTitle = 'Prove of Payment';
        $canChange = $proveFile = true;
    }
	
	if($isSubmit || $isValidation || $canProvePayment)
		$canChange = true;
	
}

if (in_array($userType, [User::TYPE_MAKER, User::TYPE_MANAGER])) {
	
	$hasMaker = is_numeric($item->id_user_maker) && $item->id_user_maker > 0;
    $needAssign = !$hasMaker && in_array($item->last_status, [CertificateStatus::STATUS_SUBMITTED]);
    $canPrint = in_array($item->last_status, [CertificateStatus::STATUS_EMITTED]);
	$lastIDRulesToValidation = !$needAssign && in_array($item->last_status, [CertificateStatus::STATUS_SUBMITTED]);
	$lastIDRulesToValidate = !$needAssign && in_array($item->last_status, [CertificateStatus::STATUS_TO_VALIDATE]);
	$lastIDRulesToDraftFile = !$needAssign && in_array($item->last_status, [CertificateStatus::STATUS_PROCESS]);
	$canToDraftFile = $lastIDRulesToDraftFile;
	$canToValidate = $lastIDRulesToValidation;
	
	$canToArcValidation = $lastIDRulesToValidate;
	
	if($needAssign)
		$changeStatusTitle = 'Assign to me';
  
	
	if($canToValidate) {
		$changeStatusTitle = 'Validate / Reject';
		$canValidate = 1;
		
		if($item->last_status != CertificateStatus::STATUS_DRAFT_VALIDATION)
			$canValidateList = 1;
	}
	
	if($canToDraftFile) {
		$changeStatusTitle = 'Draft File';
		$sigaDraftFile = true;
	}
    
    if($canToArcValidation) {
        $changeStatusTitle = 'Arc Validate';
        $validatedFile = true;
    }
    
    if($canPrint) {
        $changeStatusTitle = 'Certificate Closure';
        $canChange = true;
        $toPrint = true;
    }
	
	if($needAssign || $canToValidate || $canToDraftFile || $canToArcValidation)
		$canChange = true;
}
if (in_array($userType, [User::TYPE_INVOICER, User::TYPE_MANAGER])) {
	
	if ($item->last_status == CertificateStatus::STATUS_ACCEPTED) {
		$changeStatusTitle = 'Invoice';
		$canChange = true;
		$multiInvoice = true;
	}
	
	if($item->last_status == CertificateStatus::STATUS_PAYMENT_VALIDATION) {
        $changeStatusTitle = 'Validate / Reject';
        $canValidate = $canValidateList = $canChange = true;
    }
}

// Official Certificate Upload
if ($item->last_status == CertificateStatus::STATUS_VALIDATION) {
    $uploadCertificateOfficial = true;
}

$lastStatus = !empty($item->last_status) ? $item->last_status : CertificateStatus::STATUS_CREATED;
if (empty($mobile)) {
	?>
	<div class="cell data-cell id"><?= $item->primaryKey; ?></div>
	<div class="cell data-cell"><?= $item->client->company->name; ?></div>
	<div class="cell data-cell"><?= $item->requester_name; ?></div>
	<div class="cell data-cell"><?= $item->vessel_bl_nr; ?></div>
	<div class="cell data-cell"><?= $item->modified; ?></div>
	<div class="cell data-cell status <?=(isset($userStatusCssOptions[$lastStatus]))? $userStatusCssOptions[$lastStatus]: 'has-error'; ?>">
		<?=(isset($userStatusOptions[$lastStatus]))? $userStatusOptions[$lastStatus]: 'Status not found'; ?>
		<?= $item->reject ? '<br/><b class="text-danger">Rejected</b>':''; ?>
	</div>
	<div class="cell data-cell change-status">
		
		
		<?php if ($canChange) { ?>
			<button
					type="button"
					class="change-status--btn"
					data-toggle="modal"
					data-target="#change-status--modal"
					data-id="<?=$item->primaryKey; ?>"
					data-validate="<?=$canValidate; ?>"
					data-validate-list="<?=$canValidateList; ?>"
					data-prove-payment="<?=checkData($proveFile)?>"
					data-invoice="<?=checkData($multiInvoice)?>"
					data-siga-draft-file="<?=checkData($sigaDraftFile)?>"
					data-draft-validation="<?=checkData($DraftValidation)?>"
					data-validated-file="<?=checkData($validatedFile)?>"
					data-client-id="<?= $item->client->company->id ?>"
					data-to-print="<?=checkData($toPrint)?>"
					data-certificate-official="<?=$uploadCertificateOfficial ? "true" : "false" ?>">
				<?= $changeStatusTitle; ?>
			</button>
			<?php
			if ($multiInvoice == true) {
				?>
				<input type="checkbox" name="invoice" class="invoice" data-client-id="<?= $item->client->company->id ?>" data-id="<?= $item->primaryKey; ?>">
				<?php
			}
			?>
		<?php } ?>
	</div>
	<div class="cell data-cell"><?php
		
		$query = (new \yii\db\Query());
		$rowCity = $query->select(['id_city', 'name_'.Yii::$app->language])
			->from('city')
			->where(['id_city' => $item->city])
			->one();
		echo $rowCity['name_'.Yii::$app->language];
		?></div>
	<?php if ($userType == User::TYPE_MANAGER) { ?>
		<div class="cell data-cell">
			<?= (!empty($item->maker) ? $item->maker->username : '---') . ' / ' . (!empty($item->invoicer) ? $item->invoicer->username : '---'); ?>
		</div>
	<?php } ?>
	<div class="cell data-cell">
		<ul class="icons-certificates clearfix">
			<li>
				<a href="<?= Url::to("/certificates/view?id={$item->primaryKey}"); ?>"><i class="fa fa-search link-icon" aria-hidden="true"></i></a>
			</li>
			<?php
			if (in_array($userType, [User::TYPE_MAKER])) {?>
				<li>
					<a href="<?= Url::to("/certificates/update?id={$item->primaryKey}"); ?>"><i class="fa fa-pencil-square-o edit-icon" aria-hidden="true"></i></a>
				</li>
				<?php
			}
			
			if (in_array($userType, [User::TYPE_CLIENT, User::TYPE_MANAGER])) {
				if (in_array($userType, [User::TYPE_CLIENT]) && in_array($item->last_status, [
						CertificateStatus::STATUS_CREATED,
						CertificateStatus::STATUS_EDITED,
						CertificateStatus::STATUS_REJECTED,
					]) ||
					in_array($userType, [User::TYPE_MANAGER])) {
					?>
					<li>
						<a href="<?= Url::to("/certificates/update?id={$item->primaryKey}"); ?>"><i class="fa fa-pencil-square-o edit-icon" aria-hidden="true"></i></a>
					</li>
				<?php   }?>
				<li>
					<a href="<?= Url::to("/certificates/clone?id={$item->primaryKey}"); ?>"><i class="fa fa-files-o duplicate-icon" aria-hidden="true"></i></a>
				</li>
				<?php
			}
			if ($userType == User::TYPE_MANAGER) {
				?>
				<li>
					<a onclick="if (confirm('<?= Yii::t('frontend', 'Are you sure you want to delete the certificate?'); ?>')) {
							return true;
							} else {
							return false;
							}
							;
							" href="<?= Url::to("/certificates/delete?id={$item->primaryKey}"); ?>"><i class="fa fa-times close-icon" aria-hidden="true"></i></a>
				</li>
				<?php
			}
			?>
		
		</ul>
	</div>
<?php } else { ?>
	<a href="<?= Url::to("/certificates/history?id={$item->primaryKey}"); ?>">
		<div class="col-xs-8">
			<div class="flex-it">
				<div class="certificate-id--title"><?= Lx::t('frontend', 'Request ID'); ?></div>
				<div class="certificate-id"><?= $item->primaryKey; ?></div>
			</div>
			
			<div class="certificate-name"><?= $item->client->company->name; ?></div>
			<div class="certificate-company"><?= $item->requester_name; ?></div>
			<br>
			<div class="certificate-date"><?= $item->modified; ?></div>
		</div>
		<div class="col-xs-4">
			<div class="certificate-status pull-right <?= $userStatusCssOptions[1]; ?>">
				<?=$userStatusOptions[$lastStatus]; ?>
			</div>
			<?php if ($userType == User::TYPE_MANAGER) { ?>
				<div class="certificate-status pull-right">
					<?= (!empty($item->maker) ? $item->maker->username : '---') . '/' . (!empty($item->invoicer) ? $item->invoicer->username : '---'); ?>
				</div>
			<?php } ?>
		</div>
		<i class="fa fa-search magnif-icon" aria-hidden="true"></i>
	</a>
	<?php
}
