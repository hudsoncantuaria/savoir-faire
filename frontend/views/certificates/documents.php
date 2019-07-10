<?php

use common\models\CertificateStatus;
use lajax\translatemanager\helpers\Language as Lx;

$this->title = Yii::t('titles', 'Certificate Documents');

$certificateStatusOptions = CertificateStatus::getStatusOptions();
$certificateStatusCssOptions = CertificateStatus::getStatusOptions(true);

echo $this->render('_menu', ['certificate' => $certificate]);
echo $this->render('_menuMobile', ['certificate' => $certificate]);

?>

<div class="container-fluid bg-history table-content--desktop hidden-xs">
    <div class="container">
        <div class="col-md-12 bg-white">
            <div class="table-wrapper">
                <div class="small-header">
                    <div class="header-text--padding flex-between width-full">
                        <div class="small-header--title"><?=Lx::t('frontend','Certificate')?> <?= $certificate->primaryKey; ?> Documents</div>
                    </div>
                </div>
                <div class="table small-table">
                    <div class="table-row header">
                        <div class="cell title-cell text-uppercase"><?=Lx::t('frontend','date')?></div>
                        <div class="cell title-cell text-uppercase"><?=Lx::t('frontend','document type')?></div>
                        <div class="cell title-cell text-uppercase"><?=Lx::t('frontend','document')?></div>
                    </div>
                    <?php
                    foreach ($certificateDocuments as $field => $document) {

                        echo $this->render('/certificates/_documentItem', [
                            'field' => $field,
                            'document' => $document,
                            'certificate' => $certificate,
                            'certificateStatusOptions' => $certificateStatusOptions,
                            'certificateStatusCssOptions' => $certificateStatusCssOptions
                        ]);
                    }
                    ?>
	                
	                <div class="table-row">
		                <div class="cell data-cell date"><?= $certificate->modified; ?></div>
		                <div class="cell data-cell certificate"><?=Lx::t('frontend','Rejected Files')?></div>
		                <div class="cell data-cell">
                            <?=Lx::t('frontend','Rejected List with ')?> <?=count($rejectedFiles)?> <?=Lx::t('frontend','item(s)')?>
		                </div>
		                <div class="cell data-cell download-btn--wrapper">
			                <ul>
	                            <?php
                                $i = 0;
	                            foreach ($rejectedFiles as $document):
		                            $i++;
	                            ?>
		                            <li style="margin-bottom: 2px;">
						                <a href="/<?= $document->path?>" class="download-btn" target="_blank">
                                            <?=Lx::t('frontend','Rejected File')?> <?=$i?> <i class="fa fa-download" aria-hidden="true"></i>
						                </a>
		                            </li>
	                            <?php
	                            endforeach; ?>
			                </ul>
		                </div>
	                </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="table-content--mobile visible-xs">
    <ul class="certificate-mobile--list clearfix">
        <?php
        foreach ($certificateDocuments as $field => $document) {
            echo $this->render('/certificates/_documentItem', [
                'field' => $field,
                'document' => $document,
                'certificate' => $certificate,
                'certificateStatusOptions' => $certificateStatusOptions,
                'certificateStatusCssOptions' => $certificateStatusCssOptions,
                'mobile' => true
            ]);
        }
        ?>
    </ul>
</div>



