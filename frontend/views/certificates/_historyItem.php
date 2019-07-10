<?php if (empty($mobile)) { ?>
    <div class="cell data-cell date"><?= $item->created; ?></div>
    <div class="cell data-cell"><?= $item->obs; ?></div>
    <div class="cell data-cell user"><?= $item->user->username; ?></div>
    <div class="cell data-cell status <?= $certificateStatusCssOptions[$item->status]; ?>">
        <?= $certificateStatusOptions[$item->status]; ?>
    </div>
<?php } else { ?>
    <li>
        <a>
            <div class="col-xs-8">
                <div class="certificate-date"><?= $item->created; ?></div>
                <br>
                <div class="certificate-name"><?= $item->obs; ?></div>
                <div class="certificate-company"><?= $item->user->username; ?></div>
            </div>
            <div class="col-xs-4">
                <div class="certificate-status pull-right <?= $certificateStatusCssOptions[$item->status]; ?>">
                    <?= $certificateStatusOptions[$item->status]; ?>
                </div>
            </div>
        </a>
    </li>
<?php } ?>