<div class="print-certificate  no-print">
    <div class="container-fluid">
        <div class="container">
            <div class="bottom-wrapper clearfix">
                <div class="flex-center">
                    <button type="submit" class="certificates-btn print print-trigger">print/pdf certificate</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->render('/certificates/_printCertificate', [
    'certificate'=>$certificate,
    'certificateContainers'=>$certificateContainers,
    'certificateContainersTypes' => $certificateContainersTypes,
    'certificateProducts'=>$certificateProducts,
    'certificateDus' => $certificateDus,
    'requester' => $requester,
    'logo'=>'/themes/logo.jpg']); ?>

<?php
$printJs = <<<JS
$(document).ready(function () {
	$('.no-show-print').hide();
});

$('.print-trigger').click(function() {
	showTimestamp();
	window.print();
	hideTimestamp();
});

function showTimestamp() {
  $( ".timestamp" ).each(function( index ) {
	  $( this ).show();
  });
}

function hideTimestamp() {
  $( ".timestamp" ).each(function( index ) {
	  $( this ).hide();
  });
}

JS;
$this->registerJs($printJs);
?>