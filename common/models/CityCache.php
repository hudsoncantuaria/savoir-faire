<?php

namespace common\models;

use Yii;
use \common\models\base\CityCache as BaseCityCache;
use yii\helpers\ArrayHelper;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "city_cache".
 */
class CityCache extends BaseCityCache {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created', 'modified'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['modified'],
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function rules() {
        return ArrayHelper::merge(
                parent::rules(), [
                # custom validation rules
                ]
        );
    }

    public static function getCityCache($q, $featureCode, $countryCode) {
        $cityCache = CityCache::find()->where(['q' => $q, 'feature_code' => $featureCode, 'country_code' => $countryCode])->one();
        if ($cityCache) {
            return json_decode($cityCache->results);
        }

        return CityCache::getGeoname($q, $featureCode, $countryCode);
    }

    public static function getGeoname($q, $featureCode, $countryCode) {
        $url = "http://api.geonames.org/searchJSON?formatted=true&name_startsWith=".urlencode($q)."&maxRows=20&lang=pt&username=pedroreis&style=FULL&country=$countryCode&featureCode=$featureCode";
        $output = CityCache::getCurl($url);
        CityCache::saveCache($q,$featureCode,$countryCode,$output);
        return json_decode($output);
    }

    public static function saveCache($q,$featureCode,$countryCode,$output){
        $cityCache = new CityCache();
        $cityCache->q = $q;
        $cityCache->feature_code = $featureCode;
        $cityCache->country_code = $countryCode;
        $cityCache->results = $output;
        $cityCache->save();
    }

    public static function getCurl($url) {
        $ch = curl_init();
        // set url
        curl_setopt($ch, CURLOPT_URL, $url);
        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // $output contains the output string
        $output = curl_exec($ch);
        // close curl resource to free up system resources
        curl_close($ch);

        return $output;
    }

}
