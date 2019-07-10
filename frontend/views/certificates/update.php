<?php

$this->title = Yii::t('titles', 'Update Certificate | Angdocs');

echo $this->render('_menu', ['certificate' => $certificate]);
?>

    <section id="edit-certficate">
        <div class="container-fluid bg-home">
            <div class="container">
                <?= $this->render('/certificates/_form', [
                    'certificate' => $certificate,
                    'certificateDus' => $certificateDus,
                    'certificateContainers' => $certificateContainers,
                    'certificateContainersTypes' => $certificateContainersTypes,
                    'certificateTariffcodes' => $certificateTariffcodes,
                    'countries' => $countries,
                    'doc' => $doc,
                    'formId' => 'new-certificate--form',
                    'formClass' => 'edit-certificate--form',
                    'buttonTitle' => $this->title,
                    'buttonClass' => 'login-btn pull-right',
                    'disabledFields' => $disabledFields,
                    'tariffcodes' => $tariffcodes,
                    'dus' => $dus,
                    'containers' => $containers,
                    'containersTypes' => $containersTypes,
                    'maxCertificateContainers' => $maxCertificateContainers,
                    'maxCertificateContainersTypes' => $maxCertificateContainersTypes,
                    'addContainerTypes' => $addContainerTypes,
                ]); ?>
            </div>
        </div>
    </section>

<?php /*
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Are you sure?</h4>
            </div>
            <div class="modal-body">
                <p>Text to be edited by the client (Reason to reject the certificate).</p>
                <div class="flex-center">
                    <button type="button" data-dismiss="modal" class="btn-modal btn-danger">Cancel</button>
                    <button type="button" class="btn-modal btn-success">Yes</button>
                </div>
            </div>
        </div>
    </div>
</div>
 */ ?>

<?php
echo $this->render('_informationModal');
echo $this->render('_shipmentModal');

$updateJs = <<<JS
    $('#loading-date, #ets-date, #eta-date, #goods-loading-date, #goods-deliveryestimate-date').datetimepicker();
JS;

$this->registerJS($updateJs, $this::POS_READY);
