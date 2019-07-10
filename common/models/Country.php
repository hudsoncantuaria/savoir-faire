<?php

namespace common\models;

use Yii;
use \common\models\base\Country as BaseCountry;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "country".
 */
class Country extends BaseCountry
{

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
             parent::rules(),
             [
                  # custom validation rules
             ]
        );
    }
    
    public function getCountries(){
        foreach ($this->find()->all() as $item) {
            $countries[] = ['text'=>$item['name_common'], 'value'=>$item['cca2']];
        }
        return json_encode($countries);
    }
    
    public function getCountryNameByCca2($cca2){
        $country = $this->find()->where("cca2 = '{$cca2}'")->one();
        if(!empty($country->name_common))
            return $country->name_common;
    }
}
