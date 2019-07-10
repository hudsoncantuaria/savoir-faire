<?php


use common\helpers\CustomHelper;
use common\models\base\User;
use lajax\translatemanager\helpers\Language as Lx;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$controller = Yii::$app->controller->id;
$action = Yii::$app->controller->action->id;

$disabledFields = !empty($disabledFields) ? $disabledFields : [];

if (count(Yii::$app->session->getAllFlashes()) > 0) {
    ?>
    <div class="generate-alerts margin-top-20">
        <?= CustomHelper::generateAlerts(); ?>
    </div>
    <?php
}

$form = ActiveForm::begin([
    'id' => !empty($formId) ? $formId : '',
    'options' => ['class' => !empty($formClass) ? $formClass : '']
]);

if (!empty($user) && !Yii::$app->params['isManagerOrCompany']) {
    echo Html::hiddenInput('UserForm[id_user]', $user->primaryKey);
}

?>

<div class="certificate-page--wrapper margin-top-20">
    <div class="certificate-box--item">
        <div class="certificate-box--head">
            <p><?= Lx::t('frontend', 'User'); ?></p>
        </div>
        <div class="certificate-box--body">
            <div class="row <?= empty($blockClassException) ? 'd-block--mob' : ''; ?>">
                <div class="col-xs-12 col-sm-6 certificate-box--column">
                    <?php
                    echo $form->field($userForm, 'name', [
                        'options' => [
                            'class' => 'input-wrapper',
                            'tag' => 'div',
                        ],
                        'template' => '{label}{input}{error}',
                    ])->textInput(array_merge([
                        'class' => '',
                        'required' => ''
                    ], in_array('name', $disabledFields) ? ['disabled' => ''] : []));
                    ?>
                </div>
                
                <div class="col-xs-12 col-sm-6 certificate-box--column">
                    <?php
                    echo $form->field($userForm, 'email', [
                        'options' => [
                            'class' => 'input-wrapper',
                            'tag' => 'div',
                        ],
                        'template' => '{label}{input}{error}',
                    ])->textInput(array_merge([
                        'class' => '',
                        'required' => ''
                    ], in_array('email', $disabledFields) ? ['disabled' => ''] : []));
                    ?>
                </div>
                <div class="col-xs-12 col-sm-6 certificate-box--column">
                    <?php
                    echo $form->field($userForm, 'address', [
                        'options' => [
                            'class' => 'input-wrapper',
                            'tag' => 'div',
                        ],
                        'template' => '{label}{input}{error}',
                    ])->textInput(['class' => '', 'required' => '']);
                    
                    ?>
                </div>
                <div class="col-xs-12 col-sm-6 certificate-box--column no-label">
                <?php
                    $countryFieldOptions = [
                        'options' => [
                            'class' => 'input-wrapper',
                            'tag' => 'div',
                        ],
                        'template' => '{input}{error}',
                    ];
                    
                    echo CustomHelper::generateCountryDropdownlist($form, $userForm, 'id_country', ['class' => 'select-custom'], null, [], false, $countryFieldOptions);
                ?>
                </div>
                <div class="col-xs-12 col-sm-6 certificate-box--column">
                <?php
                echo $form->field($userForm, 'city', [
                    'options' => [
                        'class' => 'input-wrapper',
                        'tag' => 'div',
                    ],
                    'template' => '{label}{input}{error}',
                ])->textInput(['class' => 'select-custom']);
                ?>
                </div>
                <div class="col-xs-12 col-sm-6 certificate-box--column">
                <?php
                echo $form->field($userForm, 'postal_code', [
                    'options' => [
                        'class' => 'input-wrapper',
                        'tag' => 'div',
                    ],
                    'template' => '{label}{input}{error}',
                ])->textInput(['class' => '', 'required' => '']);
                ?>
                </div>
	
				<div class="col-xs-12 col-sm-6 certificate-box--column">
			        <?php
			        echo $form->field($userForm, 'delivery_place', [
			            'options' => [
			                'class' => 'input-wrapper',
			                'tag' => 'div',
			            ],
			            'template' => '{label}{input}{error}',
			        ])->textInput(['class' => '']);
			        ?>
				</div>

                <div class="col-xs-12 col-sm-6 certificate-box--column">
                    <?php
                    echo $form->field($userForm, 'phone', [
                        'options' => [
                            'class' => 'input-wrapper',
                            'tag' => 'div',
                        ],
                        'template' => '{label}{input}{error}',
                    ])->textInput(['class' => '', 'required' => '', 'type' => 'tel']);
                    ?>
                </div>
        
        <?php if ((Yii::$app->params['isManagerOrCompany'] && empty($userForm->id_user))) { ?>
                <div class="col-xs-12 col-sm-6 certificate-box--column">
                <?php
                echo $form->field($userForm, 'fax', [
                    'options' => [
                        'class' => 'input-wrapper',
                        'tag' => 'div',
                    ],
                    'template' => '{label}{input}{error}',
                ])->textInput(['class' => '']);
                ?>
                </div>
                <div class="col-xs-12 col-sm-6 certificate-box--column">
                <?php
                echo $form->field($userForm, 'vat_code', [
                    'options' => [
                        'class' => 'input-wrapper',
                        'tag' => 'div',
                    ],
                    'template' => '{label}{input}{error}',
                ])->textInput(['class' => '']);
                ?>
                </div>
                <div class="col-xs-12 col-sm-6 certificate-box--column">
                <?php
                echo $form->field($userForm, 'vat', [
                    'options' => [
                        'class' => 'input-wrapper',
                        'tag' => 'div',
                    ],
                    'template' => '{label}{input}{error}',
                ])->textInput(['class' => '', 'required' => '']);
                ?>
                </div>
            <?php
        }?>
	
		<div class="col-xs-12 col-sm-6 certificate-box--column">
	        <?php
	        echo $form->field($userForm, 'username', [
	            'options' => [
	                'class' => 'input-wrapper',
	                'tag' => 'div',
	            ],
	            'template' => '{label}{input}{error}',
	        ])->textInput(['class' => '']);
	        ?>
		</div>
		<div class="col-xs-12 col-sm-6 certificate-box--column">
	        <?php
	        echo $form->field($userForm, 'password', [
	            'options' => [
	                'class' => 'input-wrapper',
	                'tag' => 'div',
	            ],
	            'template' => '{label}{input}{error}',
	        ])->textInput(['class' => '', 'type' => 'password']);
	        ?>
		</div>


		<?php
        if ($controller == 'users' && in_array($action, ['create', 'update'])) {
            ?>
                <div class="col-xs-12 col-sm-6 certificate-box--column">
                <?php
                if (Yii::$app->user->identity->type == User::TYPE_MANAGER) {
                    echo $form->field($userForm, 'type', [
                        'options' => [
                            'class' => 'input-wrapper',
                            'tag' => 'div',
                        ],
                        'template' => '{label}{input}{error}',
                    ])->dropDownList(User::getTypeOptions(), ['prompt' => 'Type', 'class' => 'select-custom']);
                }
                ?>
                </div>
                <div class="col-xs-12 col-sm-6 certificate-box--column">
                <?php
                echo $form->field($userForm, 'status', [
                    'options' => [
                        'class' => 'input-wrapper',
                        'tag' => 'div'
                    ],
                    'template' => '{label}{input}{error}',
                ])->dropDownList(Yii::$app->user->identity->type != User::TYPE_MANAGER ? User::getStatusPartialOptions() : User::getStatusOptions(), ['prompt' => 'Status', 'class' => 'select-custom']);
                ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
}

echo Html::submitButton('<span>' . (!empty($buttonTitle) ? $buttonTitle : Lx::t('frontend', 'Save')) . '</span>
                    <i class="fa fa-arrow-right" aria-hidden="true"></i>', [
    'class' => !empty($buttonClass) ? $buttonClass : ''
]);

ActiveForm::end();

$urlCitySearch = Url::to(['city/search-city']);
$bloodhoundJS = <<<JS
var citiesFunc = function(target){
    var bloody = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
        url: "$urlCitySearch",
        prepare: function(search,settings){
            settings.url += "?q="+search;
            settings.url += "&country=" + target.val();
            return settings;
        },
        rateLimitWait: 500,
        rateLimitBy: 'throtle',
      }
    });
    return bloody;
}

$('#userform-city').typeahead(null, {
    name: 'city',
    display: 'value',
    source: citiesFunc($("#userform-id_country")),
    limit: 10,
    min: 3,
    hint: true,
    highlight:true,
});
JS;
$toasterJS = <<<JS
	$('button[type="submit"').click(function(event) {
		
        let time = 999999;
        
        $('.alert-danger').each(function( index ) {
            $(this).remove();
        });


		$('input').each(function( index ) {
            let field = $(this)[0];

            if(!field.checkValidity()){
                let label = $(this).parent().find('.control-label').text();
                
                $.toaster({
                    priority : 'danger',
                    title : 'Error',
                    message : field.validationMessage+' '+label,
                    time : time
                });
            }
		});
	});
JS;

//aria-invalid="false"

$this->registerJS($bloodhoundJS);
$this->registerJS($toasterJS);