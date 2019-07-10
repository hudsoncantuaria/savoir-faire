<?php

use lajax\translatemanager\helpers\Language as Lx;


if (empty($mobile)) {
    ?>
    <div class="cell data-cell date"><?= $item->docsCertificate->created; ?></div>
    <div class="cell data-cell certificate"><?= $item->docsCertificate->name; ?></div>
    <div class="cell data-cell download-btn--wrapper">
        <a class="download-btn" target="_blank" href="/<?= $item->docsCertificate->path; ?>" data-pjax="0">
            <?= Lx::t('frontend', 'download'); ?><i class="fa fa-download" aria-hidden="true"></i>
        </a>
    </div>
<?php } else { ?>
    <a href="/<?= $item->docsCertificate->path; ?>" target="_blank" data-pjax="0">
        <div class="col-xs-12">
            <div class="certificate-date"><?= $item->docsCertificate->created; ?></div>
            <br>
            <div class="certificate-name"><?= $item->docsCertificate->name; ?></div>
        </div>
    </a>
<?php } ?>
