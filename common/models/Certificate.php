<?php

namespace common\models;

use \common\models\base\Certificate as BaseCertificate;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "certificate".
 */
class Certificate extends BaseCertificate {
	
	const PAGE_SIZE = 10;
	
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
	
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return ArrayHelper::merge(parent::rules(), []);
	}
	
	/**
	 * @param int $perPage
	 * @param null $search
	 * @param string $date
	 * @param array $exceptionIds
	 *
	 * @param bool $withOfficialDoc
	 *
	 * @return \yii\data\ActiveDataProvider
	 */
	public static function provider($perPage = self::PAGE_SIZE, $page = null) {
		$query = self::find();
		
		$dataProviderParams = ['query' => $query, 'pagination' => ['pageSize' => $perPage, 'page' => ($page-1)]];
		$dataProvider = new ActiveDataProvider($dataProviderParams);
		
		return $dataProvider;
	}
	
	public static function findOne($id){
		// this is almost copy/past from provider function
		$query = self::find();
		$query->where(['id_deal' => $id]);
		
		
		return $query->one();
	}
}
