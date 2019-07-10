<?php

namespace common\models;

use Yii;
use \common\models\base\Invoices as BaseInvoices;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "invoices".
 */
class Invoices extends BaseInvoices {

    const PAGE_SIZE = 10;

    /**
     * @param int $perPage
     *
     * @return \yii\data\ActiveDataProvider
     */
    public static function provider($perPage = self::PAGE_SIZE, $filter = null, $paymentMethod = null, $otherPaymentProof = null) {
        $query = self::find()
            ->innerjoin('invoices_certificates', 'invoices_certificates.id_invoice = invoices.id_invoice')
            ->innerjoin('certificate as c', 'c.id_certificate = invoices_certificates.id_certificate');

        $user = Yii::$app->user->identity;
        if ($user->type == User::TYPE_CLIENT) {
            // verify if is top user from company
            if (empty($user->id_user)) {
                // get all users IDs from company (top user) and merge with user IDs array
                $usersIds = array_column(User::find()
                    ->select('id')
                    ->where(['id_user' => $user->id])
                    ->asArray()
                    ->all(), 'id');
            }
            $usersIds[] = $user->id;
            $query->andWhere(['in', 'c.id_user_client', $usersIds]);
        } else if ($user->type == User::TYPE_MAKER) {
            $query->andWhere("c.id_user_maker = {$user->id}");
        } else if ($user->type == User::TYPE_INVOICER) {
            $query->andWhere("c.id_user_invoicer = {$user->id}");
        }
	
	
	    if(!is_null($filter)){
		    $query->orWhere("nr LIKE '%{$filter}%' ")
			    ->orWhere("c.id_certificate = '{$filter}' ")
                ->orWhere("c.vessel_bl_nr = '{$filter}' ")
                ->orWhere("invoices.id_invoice = '{$filter}' ")
                ->orWhere("invoices.total LIKE '%{$filter}%' ")
                ->orWhere("invoices.price LIKE '%{$filter}%' ");
	    }
    
        if(!is_null($paymentMethod))
            $query->where("c.payment_method LIKE '$paymentMethod' and c.other_payment_proof != ''");

	    if(!is_null($otherPaymentProof)){ 
            $query->where("c.other_payment_proof LIKE '%{$otherPaymentProof}%' AND c.payment_method IS NOT NULL");}
        
        
        $query->orderBy('invoices.created DESC');

        $dataProviderParams = ['query' => $query, 'pagination' => ['pageSize' => $perPage]];

        $dataProvider = new ActiveDataProvider($dataProviderParams);

        return $dataProvider;
    }

}
