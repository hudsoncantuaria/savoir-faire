<?php

$this->title = Yii::t('titles', 'View Certificate | Camdocs');

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

<?php
echo $this->render('_informationModal');
echo $this->render('_shipmentModal');

$viewJs = <<<JS
    $('#loading-date, #ets-date, #eta-date').datetimepicker();

    $('input[type=text]').each(function() {
        $(this).addClass('trigger-label');
    });
JS;

$this->registerJS($viewJs, $this::POS_READY);
