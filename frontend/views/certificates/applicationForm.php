<?php

use common\models\Certificate;
?>
<section id="document-header">
    <div class="container">
        <div class="row">
            <div class="col-md-12 center text-center"><img src="<?= Yii::getAlias('@theme/images/coat_of_arms_of_Angola.jpg'); ?>"></div>
            <div class="col-md-12 center text-center">REPÚBLICA DE ANGOLA</div>
            <div class="col-md-12 center text-center"> &block; &block; &block; &block;</div>
            <div class="col-md-12 center text-center"><strong>MINISTÉRIO DOS TRANSPORTES</strong></div>
            <div class="col-md-12 center text-center"><strong>CONSELHO NACIONAL DE CARREGADORES</strong></div>
            <div class="col-md-12 center text-center">APPLICATION FORM FOR THE ISSUE OF LOADING CERTIFICATES</div>

        </div>
    </div>
</section>

<section id="document-application--form">
    <div class="container">
        <div class="row request-header">
            <table border="1" width="100%">
                <tr>
                    <td  width="40%">
                        Request Nr: <?= $certificate->id_certificate ?>
                        <br>
                        REQUESTED BY: (COMPANY /
                    </td>

                    <td width="60%">
                        <?= $certificate->requester_name ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        EMAIL: <?= $certificate->requester_email ?>
                    </td>
                </tr>
            </table>
        </div>

        <div class="row request-details--general">
            <table border="1" width="100%">
                <tr>
                    <td colspan="2" style="background-color: blue; text-align: center;">
                        <strong>
                            ** PLEASE FILL IN WITH CLARIFIED INFORMATION & SEND WITH SHIPPING LINE BILL OF LADING AND INVOICE **
                        </strong>
                    </td>
                </tr>
                <!-- IMPORTER -->
                <tr>
                    <td colspan="2">
                        <strong>IMPORTER</strong>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        1. NAME: <?= $certificate->importer_name ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        2. ADDRESS: <?= $certificate->importer_address ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        3. TAX PAYER NUMBER: <?= $certificate->importer_vat ?>
                    </td>
                </tr>
                <!-- EXPORTER -->
                <tr>
                    <td colspan="2">
                        <strong>EXPORTER</strong>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        4. NAME: <?= $certificate->exporter_name ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        5. ADDRESS: <?= $certificate->exporter_address ?> <?= $certificate->exporter_id_city ?> <?= $certificate->exporterIdCountry->name_common ?>
                    </td>
                </tr>
                <!-- OTHER INFORMATIONS -->
                <tr>
                    <td colspan="2">
                        <strong>OTHER INFORMATIONS</strong>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        6. FORWARDING AGENT: <?= $certificate->forwarding_agent ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        7. BILL OF LADING Nº: <?= $certificate->importer_address ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        8. WEIGHT AND M3 MEASURE AS PER B/L: <?= $certificate->lading_weight ?>KGS <?= $certificate->lading_volume ?>CBM
                    </td>
                </tr>
                <tr>
                    <td width="60%">
                        9. INVOICE(s) AMOUNT: <?= $certificate->cost_invoice_value ?>
                    </td>
                    <td width="40%">
                        10. CURRENCY (EUR or USD): <?= Certificate::CURRENCIES[$certificate->cost_invoice_currency] ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        10. INVOICE NUMBER: to be discussed; multi invoices ?! number 10 is repeated
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        11. DU NUMBER(S): to be discussed; multi DUs ?!
                    </td>
                </tr>
            </table>
        </div>

        <div class="row request-details--vessel">
            <table border="1" width="100%">
                <tr>
                    <td style="background-color: blue; text-align: center;">
                        <strong>
                            VESSEL DETAILS
                        </strong>
                    </td>
                </tr>
                <tr>
                    <td>
                        12. SPECIFIED SHIP & VOYAGE: <?= $certificate->vessel_name ?> (Voyage: <?= $certificate->vessel_voyage_nr ?>)
                    </td>
                </tr>
                <tr>
                    <td>
                        13. SHIPPING LINE: <?= $certificate->vessel_shipping_line ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        14. LOADING PORT: <?= $certificate->goods_loading_harbor ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        15. DESTINATION PORT: <?= $certificate->goods_destination_harbor ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        16. DATE OF LOADING: <?= $certificate->goods_loading_date ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        17. ESTIMATED TIME OF ARRIVAL: <?= $certificate->goods_deliveryestimate_date ?>
                    </td>
                </tr>
            </table>
        </div>

        <div class="row request-details--goods">
            <table border="1" width="100%" style="text-align: center">
                <tr>
                    <td colspan="7" style="background-color: blue; ">
                        <strong>
                            MODE OF TRANSPORT
                        </strong>
                    </td>
                </tr>
                <tr>
                    <td width="30%">
                        FT <br> (TYPE OF CONTAINER)
                    </td>
                    <td>
                        CONTAINER Nº
                    </td>
                    <td>
                        BULK <br>
                        (CBM)
                    </td>
                    <td>
                        WEIGHT
                        <br>
                        (MT)
                    </td>
                    <td colspan="2">
                        PACKAGES
                    </td>
                    <td>
                        FREIGHT
                    </td>
                </tr>
                <?php
                foreach($certificate->certificateChassis as $chassi){
                    ?>
                <tr>
                    <td>RORO</td>
                    <td><?= $chassi->nr ?></td>
                    <td></td>
                    <td></td>
                    <td colspan="2"></td>
                    <td></td>
                </tr>
                <?php
                }
                ?>

                <?php
                foreach($certificate->certificateContainers20 as $container20){
                    ?>
                <tr>
                    <td>20</td>
                    <td><?= $container20->nr ?></td>
                    <td></td>
                    <td></td>
                    <td colspan="2"></td>
                    <td></td>
                </tr>
                <?php
                }
                ?>

                <?php
                foreach($certificate->certificateContainers40 as $container40){
                    ?>
                <tr>
                    <td>40</td>
                    <td><?= $container40->nr ?></td>
                    <td></td>
                    <td></td>
                    <td colspan="2"></td>
                    <td></td>
                </tr>
                <?php
                }
                ?>

                <tr style="background-color: blue;">
                    <td>
                        <strong>DESCRIPTION OF GOODS</strong>
                    </td>
                    <td colspan="6">
                        <strong>GOODS DETAILS</strong>
                    </td>
                </tr>
                <tr>
                    <td>Description of Goods:</td>
                    <td>HS Code <br> (8 DIGITS)</td>
                    <td>Value</td>
                    <td>Weight <br> (MT)</td>
                    <td>Quantity</td>
                    <td>Country of Origin</td>
                    <td>Made In</td>
                </tr>

                <?php
                foreach($certificate->certificateTariffcodes as $tariffcode){
                    ?>
                <tr>
                    <td><?= $tariffcode->description?></td>
                    <td><?= $tariffcode->code ?></td>
                    <td><?= $tariffcode->value ?></td>
                    <td><?= $tariffcode->weight?></td>
                    <td><?= $tariffcode->qty?></td>
                    <td><?= $tariffcode->country->name_common ?></td>
                    <td><?= $tariffcode->country->name_common ?></td>
                </tr>
                <?php
                }
                ?>
            </table>
        </div>
    </div>
</section>