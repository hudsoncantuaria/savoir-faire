<?php

namespace common\helpers;

use lajax\translatemanager\helpers\Language as Lx;
use common\models\CertificateStatus;
use common\models\City;
use common\models\Country;
use Yii;
use yii\base\Model;
use yii\bootstrap\Alert;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;

class CustomHelper {

    public static function generateAlerts() {
        $generatedHTML = '';
        foreach (Yii::$app->session->getAllFlashes() as $alertKey => $configuration) {
            $generatedHTML .= Alert::widget([
                'body' => $configuration['message'],
                'options' => ['class' => "alert-{$configuration['status']} clear-both"]
            ]);
        }

        return $generatedHTML;
    }

    /*
     * generates a dropdown list with all the Cities
     */

    public static function generateCityDropdownlist($form, $model, $field, $options = [], $prompt = 'City', $disabledFields = []) {
        $thisOptions = ['prompt' => $prompt];
        if (in_array($field, $disabledFields)) {
            $thisOptions['disabled'] = '';
        }
        $cityDropdownList = $form->field($model, $field)
            ->dropDownList(ArrayHelper::map(City::find()
                ->all(), 'id_city', 'name_'.Yii::$app->language), array_merge($thisOptions, $options))
            ->label(false);

        return $cityDropdownList;
    }

    /*
     * generates a dropdown list with all the  Countries
     */

    public static function generateCountryDropdownlist($form, $model, $field, $options = [], $prompt = 'Country', $disabledFields = [], $overrideForceDisabled = false, $fieldOptions = [], $uniqueOption = null) {
        // if action id is VIEW then everything is disabled!
        $forceDisabled = Yii::$app->controller->action->id == 'view' && !$overrideForceDisabled ? true : false;

        $thisOptions = ['prompt' => $prompt];
        if (in_array($field, $disabledFields) || $forceDisabled) {
            $thisOptions['disabled'] = '';
        }

        $countryOptions = ArrayHelper::map($uniqueOption == null ? Country::find()->all() : Country::find()->where(['cca2' => $uniqueOption])->all(), 'cca2', 'name_common');

        $cityDropdownList = $form->field($model, $field, $fieldOptions)
            ->dropDownList($countryOptions, array_merge($thisOptions, $options))
            ->label(false);

        return $cityDropdownList;
    }

    /*
     * generates a standard input ( used and abused at certificates/create)
     */

    public static function generateInput($form, $model, $field, $options = [], $template = '', $inputOptions = [], $disabledFields = [], $overrideForceDisabled = false, $readonlyFields = [], $notRequired = false, $hidden = false) {
        //if action id is VIEW then everything is disabled!
        $forceDisabled = Yii::$app->controller->action->id == 'view' && !$overrideForceDisabled ? true : false;

        if( (!empty($disabledFields) && in_array($field,$disabledFields)) || $forceDisabled){
            $inputOptions = array_merge($inputOptions, ['disabled'=>'']);
        }
        if(!empty($readonlyFields) && in_array($field,$readonlyFields)){
            $inputOptions = array_merge($inputOptions, ['readonly'=>'']);
        }

        if (!$hidden) {
            $input = $form->field($model, $field, [
                'options' => $options,
                'template' => $template,
            ])->textInput($inputOptions);
        } else {
            $input = $form->field($model, $field, [
                'options' => $options,
                'template' => $template,
            ])->hiddenInput($inputOptions)->label(false);
        }

        return $input;
    }

    /*
     * generates a currencies dropdownlist
     */

    public static function generateCurrenciesDropdownlist($form, $model, $field, $options = [], $items, $disabledFields = []) {
        $thisOptions = ['tag' => false];
        if (in_array($field, $disabledFields)) {
            $thisOptions['disabled'] = '';
        }
        $currenciesDropdownList = $form->field($model, $field, ['template' => '{input}', 'options' => ['tag' => false]])
            ->dropDownList($items, array_merge($thisOptions, $options))
            ->label(false);

        return $currenciesDropdownList;
    }

    /*
     * generates a dropdown list with all the Product Types
     */

    public static function generateProductTypesDropdownlist($form, $model, $field, $options = [], $prompt = 'Product Type', $disabledFields = [], $overrideForceDisabled = false) {
        // if action id is VIEW then everything is disabled!
        $forceDisabled = Yii::$app->controller->action->id == 'view' && !$overrideForceDisabled ? true : false;

        $thisOptions = [];
        if (in_array($field, $disabledFields) || $forceDisabled) {
            $thisOptions['disabled'] = '';
        }
        $dropdownList = $form->field($model, $field)
            ->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\ProductType::find()
                ->all(), 'id_product_type', 'name'), array_merge([
                    'prompt' => Yii::t('frontend', $prompt),
                    'disabled' => (isset($relAttributes) && isset($relAttributes[$field]))
                ], array_merge($options, $thisOptions)))
            ->label(false);

        return $dropdownList;
    }
	
	/**
	* generates a dropdown list with all the Cities Enable
	* @author Hudson Cantuária <hudson@webcomum.com>
	* @since 02/05/2019
	*/
	
	public static function generateCitiesDropdownlist($form, $model, $field, $options = [], $prompt = 'Select a City', $disabledFields = []) {
  
		$thisOptions = [];
		if (in_array($field, $disabledFields)) {
			$thisOptions['disabled'] = '';
		}
		
		$cities = \yii\helpers\ArrayHelper::map(\common\models\City::find()
            ->all(), 'id_city', 'name_'.Yii::$app->language);
		
		$dropdownList = $form->field($model, $field)
			->dropDownList($cities, array_merge([
				'prompt' => Yii::t('frontend', $prompt),
				'disabled' => (isset($relAttributes) && isset($relAttributes[$field]))
			], array_merge($options, $thisOptions)))
			->label(false);
		
		return $dropdownList;
	}

    /*
     * generates a generic dropdownlist ( and yes, this was carved from generateCurrenciesDropdownList above)
     */

    public static function generateDropdownlist($form, $model, $field, $options = [], $items, $disabledFields = [], $overrideForceDisabled = false, $template=null, $fieldOptions=[]) {
        // if action id is VIEW then everything is disabled!
        $forceDisabled = Yii::$app->controller->action->id == 'view' && !$overrideForceDisabled ? true : false;

        $thisOptions = ['tag' => false];
        if (in_array($field, $disabledFields) || $forceDisabled) {
            $thisOptions['disabled'] = '';
        }
        
        $allOptions = [];
        if(!is_null($template)) {
            $allOptions['template'] = $template;
        }
        
        if(!is_null($fieldOptions)){
            $allOptions["options"] = $fieldOptions;
        }
        
        
        $dropdownList = $form->field($model, $field, $allOptions)
            ->dropDownList($items, array_merge($thisOptions, $options))
            ->label(false);

        return $dropdownList;
    }

    /*
     * updates $relation data on a ONE_MANY or MANY_MANY relational model
     * @param $relation is the name of the relation; i.e: for getFirstPost() relation you input 'firstPost'
     * @param $relationModel
     * @param $modelForm is the name of the main form being handled by the POST/GET action
     * @param $idModelForm is the primary key of the model related to $modelForm; this does not supports composed primary keys
     * @param array $uploadOptions if declared then
     */
    public static function updateRelationalData($relation, $relationModel, $modelForm, $idModelForm, $uploadOptions = []) {
        /*
         * this in theory works... sqn
         *
         * $POST = \Yii::$app->request->post();
        // nothing to save?
        if(!isset($POST[$relation]) && !isset($POST[ucfirst($relation)])){
            return true;
        }
        //$relationPosts = Yii::$app->request->post(ucfirst($relation), []);
        $relationPosts = $POST[ucfirst($relation)];
        */
    
        $POST = \Yii::$app->request->post();
        $relationPosts = Yii::$app->request->post(ucfirst($relation), []);

        //delete unused models
        foreach ($modelForm->$relation as $key => $data) {
            if (!array_key_exists($data->primaryKey, $relationPosts)) {
                $modelForm->unlink($relation, $modelForm->$relation[$key], true);
            }
        }
        $models = $modelForm->$relation;
    
        

        // new models
        foreach ($relationPosts as $key => $post) {
            if (!array_key_exists($key, $models)) {
                $newModel = new $relationModel();
                $newModel->$idModelForm = $modelForm->primaryKey;
                $models[$key] = $newModel;
            }
        }
        
        if (Model::loadMultiple($models, $POST)) {
            foreach ($models as $key => $model) {
                //TODO dont know why but if you remove it you mess it #shameonme Pedro Reis
                $model->$idModelForm = !empty($model->id_certificate) ? $model->id_certificate : $modelForm->primaryKey;
    
                if (!empty($uploadOptions['docModel']) && !empty($uploadOptions['folder'])) {
                    $doc = $uploadOptions['docModel'];
                    $doc->upload_du = UploadedFile::getInstance($uploadOptions['relation'], "[$key]path");
                    if (!empty($doc->upload_du)) {
                        $fileName = $uploadOptions['folder'] . 'file_' . $model->$idModelForm . '_' . $doc->upload_du->baseName . '_' . time() . '.' . $doc->upload_du->extension;
                        $fileName = preg_replace('/\s+/', '', $fileName);
                        $model->path = $fileName;
                        $doc->upload_du->saveAs($fileName);
                    }
                }
                if ($model->validate()) {
                    $model->save();
                } else {
                    //TODO show these errors somewhere? dont even know :\
                    /*echo "<pre>";
                    print_r($model);
                    echo "</pre>";
                    
                    echo "<pre>";
                    print_r($model->errors);
                    echo "</pre>";
                    exit;*/
                    
                }
            }
        }
    }

    public static function generateLastStatusDropdownlist($form, $model, $field, $options = [], $prompt = 'Change Status', $disabledFields = [], $overrideForceDisabled = false) {
        // if action id is VIEW then everything is disabled!
        $forceDisabled = Yii::$app->controller->action->id == 'view' && !$overrideForceDisabled ? true : false;

        $thisOptions = [];
        if (in_array($field, $disabledFields) || $forceDisabled) {
            $thisOptions['disabled'] = '';
        }
        $dropdownList = $form->field($model, $field)
            ->dropDownList(CertificateStatus::getStatusOptions(), array_merge([
                'prompt' => Yii::t('frontend', $prompt),
                'disabled' => (isset($relAttributes) && isset($relAttributes[$field]))
            ], array_merge($options, $thisOptions)))
            ->label(false);

        return $dropdownList;
    }
    
    public static function generateMakerDropdownlist($form, $model, $field, $options = [], $prompt = 'Change Maker', $disabledFields = [], $overrideForceDisabled = false) {
        // if action id is VIEW then everything is disabled!
        $forceDisabled = Yii::$app->controller->action->id == 'view' && !$overrideForceDisabled ? true : false;
        
        $thisOptions = [];
        if (in_array($field, $disabledFields) || $forceDisabled) {
            $thisOptions['disabled'] = '';
        }
        
        $dropdownList = $form->field($model, $field)
            ->dropDownList(CertificateStatus::getStatusOptions(), array_merge([
                'prompt' => Yii::t('frontend', $prompt),
                'disabled' => (isset($relAttributes) && isset($relAttributes[$field]))
            ], array_merge($options, $thisOptions)))
            ->label(false);
        
        return $dropdownList;
    }

}
