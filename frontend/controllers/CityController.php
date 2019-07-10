<?php

namespace frontend\controllers;

use common\controllers\Controller;
use common\models\City;
use yii\helpers\Json;

class CityController extends Controller {

    public function actionSearchCity($country = "pt") {
        $results = City::getCity($country);
        $output = [];
        if (!empty($results->geonames)) {
            foreach ($results->geonames as $city) {
                if(!array_key_exists($city->name, $output)){
                    $output[$city->name] = ['value' => $city->name];
                }
            }
        }
        echo JSON::encode($output);
        exit;
    }

    public function actionSearchHarbor($country = "pt") {
        $results = City::getHarbor($country);
        $output = [];
        if (!empty($results->geonames)) {
            foreach ($results->geonames as $city) {
                if(!array_key_exists($city->name, $output)){
                    $output[$city->name] = ['value' => $city->name];
                }
            }
        }
        echo JSON::encode($output);
        exit;
    }

}
