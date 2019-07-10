<?php

$this->title = Yii::t('titles', 'Create Certificate | Angdocs');

echo $this->render('_menu');
?>

    <section id="edit-certficate">
        <div class="container-fluid bg-home">
            <div class="container">
                <?= $this->render('/certificates/_form', [
                    'certificate' => $certificate,
                    'certificateDus' => $certificateDus,
                    'certificateContainers' => $certificateContainers,
                    'certificateTariffcodes' => $certificateTariffcodes,
                    'certificateContainersTypes' => $certificateContainersTypes,
                    'countries' => $countries,
                    'doc' => $doc,
                    'formId' => 'new-certificate--form',
                    'formClass' => 'edit-certificate--form',
                    'buttonTitle' => $this->title,
                    'buttonClass' => 'login-btn pull-right',
                    'disabledFields' => $disabledFields,
                    'readonlyFields' => $readonlyFields,
                    'tariffcodes' => [],
                    'dus' => [],
                    'containers' => [],
                    'containersTypes' => [],
                    'tariffcodes' => [],
                    'maxCertificateContainers' => $maxCertificateContainers,
                    'maxCertificateContainersTypes' => $maxCertificateContainersTypes,
                    'addContainerTypes' => $addContainerTypes,
                ]); ?>
            </div>
        </div>
    </section>

<?php
echo $this->render('_informationModal');
echo $this->render('_shipmentModal');

$datepicker = <<<JS
$(function () {
  $('#loading-date, #ets-date, #eta-date, #goods-loading-date, #goods-deliveryestimate-date').datetimepicker();
});
JS;

$this->registerJS($datepicker);

$inputValidation = <<<JS

JS;

$this->registerJS($inputValidation);
