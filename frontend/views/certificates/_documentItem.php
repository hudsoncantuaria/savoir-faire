<?php
use lajax\translatemanager\helpers\Language as Lx;

if (empty($mobile)) {
    ?>
    <div class="table-row">
        <div class="cell data-cell date"><?= $certificate->modified; ?></div>
        <div class="cell data-cell certificate"><?= $certificate->getAttributeLabel($field); ?></div>
        <div class="cell data-cell">
            <?= $document->name; ?>
        </div>
        <div class="cell data-cell download-btn--wrapper">
            <a href="/<?= $document->path?>" class="download-btn" target="_blank">
                <?=Lx::t('frontend','download')?><i class="fa fa-download" aria-hidden="true"></i>
            </a>
        </div>
    </div>
<?php } else { ?>
    <li>
        <a href="/<?= $document->path?>" class="">
            <div class="col-xs-12">
                <div class="certificate-date"><?= $certificate->modified; ?></div>
                <br>
                <div class="certificate-document--name"><?= $certificate->getAttributeLabel($field); ?></div>
                <div class="certificate-document--type"><?= $document->name; ?></div>
            </div>
            <i class="fa fa-file-o file-icon" aria-hidden="true"></i>
        </a>
    </li>
<?php } ?>
