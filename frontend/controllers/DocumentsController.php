<?php

namespace frontend\controllers;

use common\controllers\Controller;
use common\models\Certificate;
use common\models\Invoices;
use lajax\translatemanager\helpers\Language as Lx;
use yii\filters\AccessControl;

class DocumentsController extends Controller {

    public function init() {
        Lx::registerAssets();
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['list'],
                'rules' => [
                    [
                        'actions' => ['invoices', 'certificates'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * List documents.
     *
     * @param int $perPage
     *
     * @return mixed
     */
    public function actionInvoices($perPage = Invoices::PAGE_SIZE) {
	    $get = \Yii::$app->request->isGet ? \Yii::$app->request->get():null;
	    $filter = !empty($get['search_document'])? $get['search_document']: null;
	    
	    $hasPaymentMethod = isset($get['payment_method']) && (!empty($get['payment_method']) || $get['payment_method'] === '0');
        $hasOtherPaymentProof = isset($get['other_payment_proof']) && !empty($get['other_payment_proof']);
	    
        $paymentMethod = $hasPaymentMethod ? $get['payment_method']: null;
        $otherPaymentProof = $hasOtherPaymentProof ? $get['other_payment_proof']: null;
	    
        // get invoices documents provider
        $invoicesProvider = Invoices::provider($perPage, $filter, $paymentMethod, $otherPaymentProof);
        
        return $this->render('/documents/invoices/list', [
            'invoicesProvider' => $invoicesProvider
        ]);
    }

    public function actionCertificates($perPage = Certificate::PAGE_SIZE) {
	    $get = \Yii::$app->request->isGet ? \Yii::$app->request->get():null;
	    $filter = !empty($get['search_document'])? $get['search_document']: null;
	    
        // get certificates documents provider
        $certificatesProvider = Certificate::provider($perPage, $filter, 'all', [], true,null);

        return $this->render('/documents/certificates/list', [
            'certificatesProvider' => $certificatesProvider
        ]);
    }

}
