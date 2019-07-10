<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $contactForm \frontend\models\ContactForm */

use common\helpers\CustomHelper;
use lajax\translatemanager\helpers\Language as Lx;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('titles', 'Contacts | Angdocs');
?>

    <section id="contacts">
        <?= $this->render('/layouts/_menu'); ?>

        <div class="container-fluid">
            <div class="row">
                <div id="map"></div>
            </div>
        </div>

        <div class="container-fluid contact-bg">
            <section class="contacts-content">
                <div class="container">
                    <div class="col-md-12 hidden-xs"><h1 class="contact-title">contact us</h1></div>
                    <div class="col-xs-12 col-md-4">
	                    <address>
		                    <span class="title" style="font-weight: bold;"><?= Lx::t('frontend', 'MORADA ANGDOCS LISBOA'); ?></span><br/>
		                    <span class="street"><?= Lx::t('frontend', 'Rua de Moscavide, Lote 4.28.02, Loja A, Parque das Nações 1990-198'); ?></span><br/>
		                    <span class="city"><?= Lx::t('frontend', 'Lisboa'); ?></span><br/>
		                    <span class="country"><?= Lx::t('frontend', 'Portugal'); ?></span><br/>
		                    <span class="email"><b><?= Lx::t('frontend', 'E-mail'); ?></b>: lisboa@scc.com.pt</span><br/>
		                    <span class="phone"><b><?= Lx::t('frontend', 'Phone'); ?></b>: +351 218 947 140</span><br/>
		                    <span class="fax"><b><?= Lx::t('frontend', 'Fax'); ?></b>: +351 218 945 145</span><br/>
		                    <span class="lat" style="display: none;">38.779673</span>
		                    <span class="lgt" style="display: none;">-9.0948497</span>
	                    </address>
	                    <address>
		                    <span class="title" style="font-weight: bold;"><?= Lx::t('frontend', 'MORADA ANGDOCS POSTO'); ?></span><br/>
		                    <span class="street"><?= Lx::t('frontend', 'Praceta D. Nuno Àlvares Pereira, nº 20, 4º Andar Sala DY'); ?></span><br/>
		                    <span class="city"><?= Lx::t('frontend', '4450-218 Matosinhos'); ?></span><br/>
		                    <span class="country"><?= Lx::t('frontend', 'Portugal'); ?></span><br/>
		                    <span class="email"><b><?= Lx::t('frontend', 'E-mail'); ?></b>: porto@scc.com.pt</span><br/>
		                    <span class="phone"><b><?= Lx::t('frontend', 'Phone'); ?></b>: +351 229 374 125</span><br/>
		                    <span class="fax"><b><?= Lx::t('frontend', 'Fax'); ?></b>: +351 218 945 145</span><br/>
		                    <span class="lat" style="display: none;">41.177640</span>
		                    <span class="lgt" style="display: none;">-8.677220</span>
	                    </address>
                    </div>
                    <div class="col-xs-12 col-md-8">
                        <div class="visible-xs"><h1 class="contact-title"><?= Lx::t('frontend', 'contact us'); ?></h1></div>

                        <?php if (count(Yii::$app->session->getAllFlashes()) > 0) { ?>
                            <div class="generate-alerts margin-top-20">
                                <?= CustomHelper::generateAlerts(); ?>
                            </div>
                        <?php } ?>

                        <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>
                        <div class="row">
                            <?php
                            echo $form->field($contactForm, 'name', [
                                'options' => [
                                    'class' => 'col-xs-12 col-sm-6 certificate-box--column',
                                    'tag' => 'div',
                                ],
                                'template' => '<div class="input-wrapper">{label}{input}{error}</div>',
                            ])->textInput(['class' => '', 'required' => '']);

                            echo $form->field($contactForm, 'phone', [
                                'options' => [
                                    'class' => 'col-xs-12 col-sm-6 certificate-box--column',
                                    'tag' => 'div',
                                ],
                                'template' => '<div class="input-wrapper">{label}{input}{error}</div>',
                            ])->textInput(['class' => '', 'required' => '', 'type' => 'tel']);
                            
                            echo $form->field($contactForm, 'email', [
                                'options' => [
                                    'class' => 'col-xs-12 col-sm-12 certificate-box--column',
                                    'tag' => 'div',
                                ],
                                'template' => '<div class="input-wrapper">{label}{input}{error}</div>',
                            ])->textInput(['class' => '', 'required' => '']);

                            echo $form->field($contactForm, 'description', [
                                'options' => [
                                    'class' => 'col-xs-12 col-sm-12 certificate-box--column',
                                    'tag' => 'div',
                                ],
                                'template' => '<div class="input-wrapper">{label}{input}{error}</div>',
                            ])->textInput(['class' => '', 'required' => '']);
                            ?>
                        </div>
                        
                        <div class="btn-wrapper send">
                            <?php
                            echo Html::submitButton('send<i class="fa fa-paper-plane" aria-hidden="true"></i>', [
                                    'class' => 'send-btn',
                                    'name' => 'send-button'
                                ]);
                            ?>
                        </div>
                        <?php
                        ActiveForm::end();
                        ?>
                    </div>
                </div>
            </section>
        </div>
    </section>

<?php
$googleMapsJs = <<<JS

	setMapInfoByAddress($('address').first());

	$('address').click(function() {
		setMapInfoByAddress($(this));
	});

	function setMapInfoByAddress(address){
		let title = address.find('.title .language-item').html();
		let street = address.find('.street .language-item').html();
		let city = address.find('.city .language-item').html();
		let country = address.find('.country .language-item').html();
		let lat = address.find('.lat').html();
		let lgt = address.find('.lgt').html();
		let content = "<b>"+title+"</b><br/>"+
			street+"<br/>"+
			city+"<br/>"+
			country;
		
	    initialize(lat, lgt, title, content);
	}


    function initialize(lat ,long ,title ,content) {
        var coordinates = new google.maps.LatLng(lat,long);

        var mapOptions = {
            center: coordinates,
            zoom: 15,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        var map = new google.maps.Map(document.getElementById("map"),
            mapOptions);

        var marker = new google.maps.Marker({
            animation: google.maps.Animation.DROP,
            position: coordinates,
            map: map,
            title: title
        });

        var contentString = content;

        //Set window width + content
        var infowindow = new google.maps.InfoWindow({
            content: contentString,
            maxWidth: 500
        });

        google.maps.event.addListener(marker, 'click', function() {
            infowindow.open(map, marker);
        });

        google.maps.event.addDomListener(window, "resize", function() {
            var center = map.getCenter();
            google.maps.event.trigger(map, "resize");
            map.setCenter(center);
        });
    }
JS;

$this->registerJsFile('https://maps.googleapis.com/maps/api/js?key=AIzaSyCZKKiKAGJTbpKKZLPmf6HxyhM-re-ivKo', [], 'googleMapsFile');
$this->registerJS($googleMapsJs, $this::POS_READY, 'googleMaps');