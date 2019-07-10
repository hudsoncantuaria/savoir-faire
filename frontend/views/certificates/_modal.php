<?php

use yii\helpers\Html;
?>

<div class="modal fade" id="change-status--modal" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Are you sure?</h4>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<div class="validate-group">
						<?php
						echo Html::radioList('validateList', "0", [
							"0" => 'Validate',
							"1" => 'Reject'
						], ['id' => 'validate-list']);
						?>
					</div>
					<div class="validate-draft-validation-group" style="display: none">
						<?php
						echo Html::radioList('validateDraftValidationList', "0", [
							"0" => 'Validate',
							"1" => 'Reject'
						], ['id' => 'validate-draft-validation-list']);
						?>
					</div>
					
					<div class="description-group">
						<div class="input-label description">Description</div>
						<?=
						Html::textarea('description', '', [
							'id' => 'validate-description',
							'class' => 'form-control good-description',
							'rows' => 5
						]);
						?>
					</div>
					<div class="price-group">
						<div class="input-label description">Price</div>
						<?=
						Html::textInput('price', '', [
							'id' => 'validate-price',
							'class' => 'form-control',
							'type' => 'number'
						]);
						?>
					</div>
					<div class="upload-group hidden">
						<div class="input-label description">Upload</div>
						<div class="input-upload"></div>
						<div class="multi-certificates hidden"><div>
							</div>
						</div>
					</div>
					
					<div class="draft-group" style="display: none;">
						<div class="input-label description">Draft File</div>
						<div class="draft-upload"></div>
					</div>
					
					<div class="draft-validation-group" style="display: none;">
						<div class="input-label description">Reject Files</div>
						<div class="draft-validation-upload"></div>
					</div>
					
					<div class="validated-file-group hidden">
						<div class="input-label description">Validated File</div>
						<div class="validated-upload"></div>
					</div>
					
					<div class="prove-file-group hidden">
						<div class="input-label description">Proof of Payment</div>
						<div class="prove-upload"></div>
					</div>
					
					<div class="flex-center margin-top-20">
						<?php
						echo Html::hiddenInput('changeStatus', 0, ['id' => 'change-status']);
						echo Html::hiddenInput('idCertificate', null, ['id' => 'id-certificate']);
						echo Html::hiddenInput('validate', 0, ['id' => 'validate']);
						echo Html::hiddenInput('validated', 0, ['id' => 'validated']);
						?>
						<button type="button" data-dismiss="modal" class="certificates-btn change-status--btn-no red">Cancel</button>
						<button type="button" class="certificates-btn margin-left-20 validate-btn reject-btn red">Reject</button>
						<button type="button" class="certificates-btn margin-left-20 validate-btn accept-btn">Validate</button>
						<button type="button" class="certificates-btn change-status--btn-yes margin-left-20">Yes</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
