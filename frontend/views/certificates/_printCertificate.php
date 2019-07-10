<!doctype html>
<html>
<head>
    <meta charset="utf-8">
</head>
<style type="text/css">
    html, body, div, span, object, iframe,
    h1, h2, h3, h4, h5, h6, p, a, blockquote, pre,
    abbr, address, cite, code,
    del, dfn, em, img, ins, kbd, q, samp,
    small, strong, sub, sup, var,
    b, i,
    dl, dt, dd, ol, ul, li,
    fieldset, form, label, legend,
    table, caption, tbody, tfoot, thead, tr, th, td,
    article, aside, canvas, details, figcaption, figure,
    footer, header, hgroup, menu, nav, section, summary,
    time, mark, audio, video {
        margin:0;
        padding:0;
        outline:0;
        vertical-align:baseline;
        -moz-box-sizing: border-box;
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
        
    }
    
    body {
        line-height:1;
        background-color: #fff;
        width: 100%;
        height: 100%;
        font-family: Arial;
        font-size: 12px;
        line-height: 18px;
        -webkit-print-color-adjust:exact;
        color-adjust: exact;
    }
    
    a {
        font-size:100%;
        background:transparent;
    }
    
    table {
        border-collapse:collapse;
        border-spacing:0;
    }
    
    input, select {
        vertical-align:middle;
    }
    
    .main:not(:first-child) {
        page-break-before: always;
    }
    
    .main {
        margin: auto;
        width: 100%;
        max-width: 890px;
        height: 100%;
        display: block;
        background: #fff;
    }
    
    @media print {
        body{
            width: 24cm;
            height: 30cm;
            margin: auto;
            background-color: #fff;
        }
        
        * {
            overflow: visible !important;
        }
    }
    
    @page {
        size: A4;
        margin: 4mm 4mm;
    }
    
    .clearfix:before, .clearfix:after, .clearfix > .lastcol:after, .ffluid:after, br.cb{display:block; height:0 !important; line-height:0; overflow:hidden; visibility:hidden}
    .clearfix:before, .clearfix:after{content:"."}
    .clearfix:after{clear:both}
    
    .w2 {
        width: 2%;
    }
    
    .w5 {
        width: 5%;
    }
    
    .w20 {
        width: 20%;
    }
    
    .w30 {
        width: 30%;
    }
    
    .w35 {
        width: 35%;
    }
    
    .w40 {
        width: 40%
    }
    
    .w45 {
        width: 45%
    }
    
    .w50 {
        width: 50%
    }
    
    .w55 {
        width: 55%
    }
    
    .w60 {
        width: 60%
    }
    
    .w65 {
        width: 65%
    }
    
    .w70 {
        width: 70%
    }
    
    .w80 {
        width: 70%
    }
    
    .w100 {
        width: 100%
    }
    
    .uppercase {
        text-transform: uppercase;
    }
    
    .centered {
        text-align: center;
    }
    
    .semi-bold {
        font-weight: 500;
    }
    
    .bold {
        font-weight: bold;
    }
    
    .v-align-middle {
        vertical-align: middle;
    }
    
    .v-align-top {
        vertical-align: top;
    }
    
    .v-align-bottom {
        vertical-align: bottom;
    }
    
    .align-right {
        text-align: right;
    }
    
    .align-left {
        text-align: left;
    }
    
    .align-center {
        text-align: center;
    }
    
    .times {
        font-family: 'Times';
    }
    
    .font-10 {
        font-size: 10px;
    }
    
    .font-11 {
        font-size: 11px;
    }
    
    .font-12 {
        font-size: 12px;
    }
    
    .font-13 {
        font-size: 13px;
    }
    
    .font-14 {
        font-size: 14px;
    }
    
    .margin-bottom-10 {
        margin-bottom: 10px;
    }
    
    .margin-bottom-15 {
        margin-bottom: 15px;
    }
    
    .margin-bottom-20 {
        margin-bottom: 20px;
    }
    
    table {
        border-color: #000;
    }
    
    table td {
        padding: 5px;
    }
    
    .header-table img {
        max-width: 120px;
    }
    
    .no-border-top {
        border-top: 0;
    }
    
    .blue {
        background-color: #64a0fb;
    }
    
    div.timestamp {
	    position: absolute;
	    right: 0;
	    display: none;
    }

</style>

<body>
	<span class="main">
		<table class="w100 header-table margin-bottom-15">
			<tr>
				<td align="center">
					<img src="<?=$logo?>" width="120">
					<p class="uppercase font-12">Républica de Angola</p>
					<p class="uppercase bold times font-14">Ministério dos transportes</p>
					<p class="uppercase bold times font-14">Conselho Nacional de Carregadores</p>
					<p class="uppercase semi-bold font-14"><?=Yii::t('frontend','application form for the issue of loading certificates')?></p>
				</td>
			</tr>
		</table>
		<table class="w100" border="1">
			<tr>
				<td class="w30">
					<p class="uppercase bold font-11"><?=Yii::t('frontend','Certificate Requested by')?>:</p>
					<p class="uppercase bold font-11"><?=Yii::t('frontend','(Company / name)')?></p>
				</td>
				<td class="w70 v-align-middle">
					<p class="uppercase bold font-11"><?=$certificate->requester_name?></p>
				</td>
			</tr>
		</table>
		<table class="w100 no-border-top" border="1">
			<tr>
				<td class="w50">
					<p><span class="uppercase bold font-11"><?=Yii::t('frontend','Email')?>:</span> <?=$certificate->requester_email?></p>
				</td>
				<td class="w50">
					<p><span class="uppercase bold font-11"><?=Yii::t('frontend','Contact N.(TEL)')?>::</span> <?= isset($requester->phone) && !empty($requester->phone) ? $requester->phone: ''?></p>
				</td>
			</tr>
		</table>
		<table class="w100 no-border-top margin-bottom-15" border="1">
			<tr>
				<td class="w100" align="center">
					<p><span class="uppercase bold font-11"><?=Yii::t('frontend','Invoice to')?>:</span> <?=$certificate->requester_name?></p>
				</td>
			</tr>
		</table>
		<table class="w100 margin-bottom-15" border="1">
			<tr>
				<td class="w100 blue" align="center" colspan="2">
					<p class="uppercase bold font-11">**  <?=Yii::t('frontend','PLEASE FILL IN WITH CLARIFIED INFORMATION & SEND WITH SHIPPING LINE BILL OF LADING AND INVOICE')?>  **</p>
				</td>
			</tr>
			<tr>
				<td class="w100" align="left" colspan="2">
					<p class="uppercase bold font-12"><?=Yii::t('frontend','Email')?></p>
				</td>
			</tr>
			<tr>
				<td class="w100" align="left" colspan="2">
					<p class="uppercase font-11">1. <?=Yii::t('frontend','NAME')?>: <?=$certificate->importer_name?></p>
				</td>
			</tr>
			<tr>
				<td class="w100" align="left" colspan="2">
					<p class="uppercase font-11">2. <?=Yii::t('frontend','ADDRESS')?>: <?=$certificate->importer_address?> - <?=$certificate->importer_id_city?>-<?=$certificate->importer_id_country['name']?></p>
				</td>
			</tr>
			<tr>
				<td class="w100" align="left" colspan="2">
					<p class="uppercase font-11">3. <?=Yii::t('frontend','TAXPAYER NUMBER')?>: <?=$certificate->importer_vat?></p>
				</td>
			</tr>
			<tr>
				<td class="w100" align="left" colspan="2">
					<p class="uppercase bold font-12"><?=Yii::t('frontend','Exporter')?></p>
				</td>
			</tr>
			<tr>
				<td class="w100" align="left" colspan="2">
					<p class="uppercase font-11">4. <?=Yii::t('frontend','NAME')?>: <?=$certificate->exporter_name?></p>
				</td>
			</tr>
			<tr>
				<td class="w100" align="left" colspan="2">
					<p class="uppercase font-11">5. <?=Yii::t('frontend','ADDRESS')?>: <?=$certificate->exporter_address?> - <?=$certificate->exporter_id_city?>-<?=$certificate->exporter_id_country['name']?></p>
				</td>
			</tr>
			<tr>
				<td class="w100" align="left" colspan="2">
					<p class="uppercase bold font-12"><?=Yii::t('frontend','OTHER INFORMATIONS')?></p>
				</td>
			</tr>
			<tr>
				<td class="w100" align="left" colspan="2">
					<p class="uppercase font-11">6. <?=Yii::t('frontend','FORWARDING AGENT')?>: <?=$certificate->forwarding_agent?></p>
				</td>
			</tr>
			<tr>
				<td class="w100" align="left" colspan="2">
					<p class="uppercase font-11">7. <?=Yii::t('frontend','BILL OF LADING')?>  Nº <?=$certificate->lading_packages_nr?></p>
				</td>
			</tr>
			<tr>
				<td class="w100" align="left" colspan="2">
					<p class="uppercase font-11">8. <?=Yii::t('frontend','WEIGHT AND M3 MEASURE AS PER B/ L')?>: <?=$certificate->lading_weight?></p>
				</td>
			</tr>
			<tr>
				<td class="w60" align="left">
					<p class="uppercase font-11">9. <?=Yii::t('frontend','INVOICE(S) AMOUNT')?>: <?=number_format($certificate->cost_invoice_value, 2,'.',',')?></p>
				</td>
				<td class="w40" align="left">
					<p class="uppercase font-11">10. <?=Yii::t('frontend','CURRENCY (EUR or USD)')?>:
                        <?=$certificate->currency === 1? 'EUR':''?>
                        <?=$certificate->currency === 2? 'USD':''?>
					</p>
				</td>
			</tr>
			<tr>
				<td class="w100" align="left" colspan="2">
					<p class="uppercase font-11">10. <?=Yii::t('frontend','INVOICE NUMBER')?>: <?=$certificate->invoice_number?></p>
				</td>
			</tr>
			<tr>
				<td class="w100" align="left" colspan="2">
					<p class="uppercase font-11">11. <?=Yii::t('frontend','LICENCIAMENTO NR')?>:
                        <?php
                        $dusCodes='';
                        foreach ($certificateDus as $dus)
                            $dusCodes .= $dus->name.', ';
                        if(!empty($dusCodes))
                            echo substr($dusCodes, 0, -2);;
                        ?></p>
				</td>
			</tr>
		</table>
		<table class="w100 margin-bottom-15" border="1">
			<tr>
				<td class="w100 blue" align="center" colspan="2">
					<p class="uppercase bold font-11"><?=Yii::t('frontend','VESSEL DETAILS')?></p>
				</td>
			</tr>
			<tr>
				<td class="w100" align="left" colspan="2">
					<p class="uppercase font-11">12. <?=Yii::t('frontend','SPECIFIED SHIP & VOYAGE')?>: <?=$certificate->vessel_name?></p>
				</td>
			</tr>
			<tr>
				<td class="w100" align="left" colspan="2">
					<p class="uppercase font-11">13. <?=Yii::t('frontend','SHIPPING LINE')?>: <?=$certificate->vessel_shipping_line?></p>
				</td>
			</tr>
			<tr>
				<td class="w100" align="left" colspan="2">
					<p class="uppercase font-11">14. <?=Yii::t('frontend','LOADING PORT')?>: <?=$certificate->goods_loading_harbor?></p>
				</td>
			</tr>
			<tr>
				<td class="w100" align="left" colspan="2">
					<p class="uppercase font-11">15. <?=Yii::t('frontend','DESTINATION PORT')?>: <?=$certificate->goods_destination_harbor?></p>
				</td>
			</tr>
			<tr>
				<td class="w100" align="left" colspan="2">
					<p class="uppercase font-11">16. <?=Yii::t('frontend','DATE OF LOADING')?>: <?=date('d.m.y',strtotime($certificate->goods_loading_date))?></p>
				</td>
			</tr>
			<tr>
				<td class="w100" align="left" colspan="2">
					<p class="uppercase font-11">17. <?=Yii::t('frontend','ESTIMATED TIME OF ARRIVAL')?>: <?=date('d.m.y',strtotime($certificate->goods_deliveryestimate_date))?></p>
				</td>
			</tr>
		</table>
		<table class="w100 margin-bottom-15" border="1">
			<tr>
				<td class="w100 blue" align="center" colspan="6">
					<p class="uppercase bold font-11"><?=Yii::t('frontend','MODE OF TRANSPORT')?></p>
				</td>
			</tr>
			<tr>
				<td align="center" class="v-align-middle">
					<p class="uppercase font-11"><?=Yii::t('frontend','FT (TYPE OF CONTAINER)')?></p>
				</td>
				<td align="center" class="v-align-middle">
					<p class="uppercase font-11"><?=Yii::t('frontend','CONTAINER')?> Nº</p>
				</td>
				<td align="center" class="v-align-middle">
					<p class="uppercase font-11"><?=Yii::t('frontend','BULK (CBM)')?></p>
				</td>
				<td align="center" class="v-align-middle">
					<p class="uppercase font-11"><?=Yii::t('frontend','WEIGTH (MT)')?></p>
				</td>
				<td align="center" class="v-align-middle">
					<p class="uppercase font-11"><?=Yii::t('frontend','PACKAGES')?></p>
				</td>
				<td align="center" class="v-align-middle">
					<p class="uppercase font-11"><?=Yii::t('frontend','FREIGHT')?></p>
				</td>
			</tr>
            <?php
            $counterContainers = $containersPages = 0;
            $containersFirstLineLimit = 7;
            $containersOthersLineLimit = 40;
            foreach ($certificateContainersTypes as $container) {
            $counterContainers++;
            ?>
            <?php
            if (($counterContainers >= $containersFirstLineLimit && $containersPages == 0) || ($containersPages > 0 && $counterContainers >= $containersOthersLineLimit)) {
            $counterContainers = 1;
            $containersPages += $containersPages == 0? 1 : 0;
            $containersPages++;
            ?>
				</table>
				<div class="timestamp"><?= time() ?></div>
		
				<table class="w100 margin-bottom-15" border="1" style="page-break-before:always">
					<tr>
						<td class="w100 blue" align="center" colspan="6">
							<p class="uppercase bold font-11"><?= Yii::t('frontend', 'MODE OF TRANSPORT') ?></p>
						</td>
					</tr>
					<tr>
						<td align="center" class="v-align-middle">
							<p class="uppercase font-11"><?= Yii::t('frontend', 'FT (TYPE OF CONTAINER)') ?></p>
						</td>
						<td align="center" class="v-align-middle">
							<p class="uppercase font-11"><?= Yii::t('frontend', 'CONTAINER') ?> Nº</p>
						</td>
						<td align="center" class="v-align-middle">
							<p class="uppercase font-11"><?= Yii::t('frontend', 'BULK (CBM)') ?></p>
						</td>
						<td align="center" class="v-align-middle">
							<p class="uppercase font-11"><?= Yii::t('frontend', 'WEIGTH (MT)') ?></p>
						</td>
						<td align="center" class="v-align-middle">
							<p class="uppercase font-11"><?= Yii::t('frontend', 'PACKAGES') ?></p>
						</td>
						<td align="center" class="v-align-middle">
							<p class="uppercase font-11"><?= Yii::t('frontend', 'FREIGHT') ?></p>
						</td>
					</tr>
                    <?php
                    }
                    ?>
					<tr>
						<td align="center" class="v-align-middle">
							<p class="uppercase font-11">
								<?php
                                $containerTypeEntity = new \common\models\ContainerTypes();
                                $containerType = $containerTypeEntity->find()->where(['id_container_type' => $container->id_container_type])->one();
                                echo $containerType->name;
                                ?></p>
						</td>
						<td align="center" class="v-align-middle">
							<p class="uppercase font-11"><?= $certificateContainers[$container->id_certificate_container_type] ?? '' ?></p>
						</td>
						<td align="center" class="v-align-middle">
							<p class="uppercase font-11"> <!--<b>[BULK TYPE]</b>--></p>
						</td>
						<td align="center" class="v-align-middle">
							<p class="uppercase font-11"><?= $container->weight ?></p>
						</td>
						<td align="center" class="v-align-middle">
							<p class="uppercase font-11"><?= $container->packages_nr ?></p>
						</td>
						<td align="center" class="v-align-middle">
							<p class="uppercase font-11"><?= $container->freight ?></p>
						</td>
					</tr>
                    <?php
                    } ?>
		</table>
        <?php
        if(!empty($certificateProducts)) {
            $productLinesFirstPage = 6;
            $productLinesOthersPages = 40;
            $limitChars = 35;
            $breakPageFirstlane = true;
            if($containersPages > 1 && $counterContainers < $containersOthersLineLimit){
                $productLinesFirstPage = $productLinesOthersPages-$counterContainers-1;
                $breakPageFirstlane = false;
            }
            
            $totalValue = $totalWeight = $totalQuantity = $counter = $pages = 0;
            
            if($breakPageFirstlane) {
                ?>
	            <div class="timestamp"><?= time() ?></div>
                <?php
            }   ?>
			<table class="w100 margin-bottom-15" border="1" <?=$breakPageFirstlane?'style="page-break-before:always"':''?>>
            <tr>
				<td class="w30 blue" align="center" colspan="1">
					<p class="uppercase bold font-11"><?= Yii::t('frontend', 'DESCRIPTION OF GOODS')?></p>
				</td>
				<td class="w70 blue" align="center" colspan="5">
					<p class="uppercase bold font-11"><?= Yii::t('frontend', 'GOODS DETAILS') ?></p>
				</td>
			</tr>
            <tr>
				<td align="center" class="v-align-middle">
					<p class="font-12"><?= Yii::t('frontend', 'Description of goods') ?>:</p>
				</td>
				<td align="center" class="v-align-middle">
					<p class="font-12"><?= Yii::t('frontend', 'HS Code (8 DIGITS)') ?></p>
				</td>
				<td align="center" class="v-align-middle">
					<p class="font-12"><?= Yii::t('frontend', 'Value') ?></p>
				</td>
				<td align="center" class="v-align-middle">
					<p class="font-12"><?= Yii::t('frontend', 'Weight (MT)') ?></p>
				</td>
				<td align="center" class="v-align-middle">
					<p class="font-12"><?= Yii::t('frontend', 'Quantity') ?></p>
				</td>
				<td align="center" class="v-align-middle">
					<p class="font-12"><?= Yii::t('frontend', 'Country of origin') ?></p>
				</td>
			</tr>
            <?php
            foreach ($certificateProducts as $product) {
                
                $countChar = strlen($product->desc_tariffcodes_product);
                
                if ($countChar >= $limitChars) {
                    $counter += ceil($countChar / $limitChars);
                } else {
                    $counter++;
                }
                $totalValue += $product->value;
                $totalWeight += $product->weight;
                $totalQuantity += $product->qty;
                ?>
                <?php
                if (($counter >= $productLinesFirstPage && $pages == 0) || ($pages > 0 && $counter >= $productLinesOthersPages)) {
                    $pages++;
                    $counter = 0; ?>
                    </table>
	                <div class="timestamp"><?= time() ?></div>
                    <table class="w100 margin-bottom-15" border="1" style="page-break-before:always">
                    <tr>
							<td class="w30 blue" align="center" colspan="1">
								<p class="uppercase bold font-11"><?= Yii::t('frontend', 'DESCRIPTION OF GOODS') ?></p>
							</td>
							<td class="w70 blue" align="center" colspan="6">
								<p class="uppercase bold font-11"><?= Yii::t('frontend', 'GOODS DETAILS') ?></p>
							</td>
						</tr>
                    <tr>
			                <td align="center" class="v-align-middle">
				                <p class="font-12"><?= Yii::t('frontend', 'Description of goods') ?>:</p>
			                </td>
			                <td align="center" class="v-align-middle">
				                <p class="font-12"><?= Yii::t('frontend', 'HS Code (8 DIGITS)') ?></p>
			                </td>
			                <td align="center" class="v-align-middle">
				                <p class="font-12"><?= Yii::t('frontend', 'Value') ?></p>
			                </td>
			                <td align="center" class="v-align-middle">
				                <p class="font-12"><?= Yii::t('frontend', 'Weight (MT)') ?></p>
			                </td>
			                <td align="center" class="v-align-middle">
				                <p class="font-12"><?= Yii::t('frontend', 'Quantity') ?></p>
			                </td>
			                <td align="center" class="v-align-middle">
				                <p class="font-12"><?= Yii::t('frontend', 'Country of origin') ?></p>
			                </td>
		                </tr>
                    <?php
                }
                ?>
					
				<tr>
						<td align="center" class="v-align-middle">
							<p class="uppercase font-11"><?= $product->desc_tariffcodes_product ?></p>
						</td>
						<td align="center" class="v-align-middle">
							<p class="uppercase font-11"><?= $product->code ?></p>
						</td>
						<td align="center" class="v-align-middle">
							<p class="uppercase font-11"><?= number_format($product->value, 2, '.', ',') ?></p>
						</td>
						<td align="center" class="v-align-middle">
							<p class="uppercase font-11"><?= number_format($product->weight, 2, '.', ',') ?></p>
						</td>
						<td align="center" class="v-align-middle">
							<p class="uppercase font-11"><?= number_format($product->qty, 2, '.', ',') ?></p>
						</td>
						<td align="center" class="v-align-middle">
							<p class="uppercase font-11"><?php
                                $countryEntity = new \common\models\Country();
                                echo $countryEntity->getCountryNameByCca2($product->id_country)
                                ?></p>
						</td>
					</tr>
                <?php
            }
            ?>
            <tr class="total">
				<td align="center" class="v-align-middle">
					<p class="uppercase bold font-12"></p>
				</td>
				<td align="center" class="v-align-middle">
					<p class="uppercase bold font-12">total:</p>
				</td>
				<td align="center" class="v-align-middle">
					<p class="uppercase bold font-12"><?= number_format($totalValue, 2, '.', ',') ?></p>
				</td>
				<td align="center" class="v-align-middle">
					<p class="uppercase bold font-12"><?= number_format($totalWeight, 2, '.', ',') ?></p>
				</td>
				<td align="center" class="v-align-middle">
					<p class="uppercase bold font-12"><?= number_format($totalQuantity, 2, '.', ',') ?></p>
				</td>
				<td align="center" class="v-align-middle">
					<p class="uppercase bold font-12"></p>
				</td>
			</tr>
            </table>
        <?php }?>
		<div class="timestamp"><?= time() ?></div>
	</span>
</body>
</html>
