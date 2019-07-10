<?php

namespace frontend\controllers;

use common\controllers\Controller;
use common\models\ContainerTypes;
use common\models\Country;
use common\models\Doc;
use common\models\City;
use common\models\User;
use common\models\Certificate;
use common\models\CertificateDus;
use common\models\CertificateStatus;
use common\models\CertificateChassis;
use common\models\CertificateGuichet;
use common\models\CertificateContainers;
use common\models\CertificateContainersTypes;
use common\models\CertificateTranshipments;
use common\models\CertificateTariffcodes;
use common\models\TariffcodesClass;
use common\models\TariffcodesCategory;
use common\models\TariffcodesProduct;
use common\helpers\CustomHelper;
use lajax\translatemanager\helpers\Language as Lx;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\HttpException;
use yii\web\UploadedFile;
use yii\helpers\Json;
use frontend\components\HtmlToPDF\HtmlToPDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class CertificatesController extends Controller
{
    
    public function init()
    {
        Lx::registerAssets();
        parent::init();
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['create', 'update'],
                'rules' => [
                    [
                        'actions' => ['create', 'update'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [//                    'logout' => ['post'],
                ],
            ],
        ];
    }
    
    /**
     * Creates a new Certificate model.
     * If creation is successful, the browser will be redirected to the 'update' page.
     *
     * @return mixed
     * @throws \yii\db\Exception
     */
    public function actionCreate()
    {
        $doc = new Doc();
        $countriesEntity = new Country();
        $certificate = new Certificate();
        $certificateContainers = new CertificateContainers();
        $certificateContainersTypes = new CertificateContainersTypes();
        $certificateTariffcodes = new CertificateTariffcodes();
        $certificateDus = new CertificateDus();
        $containerTypesModel = new ContainerTypes();
    
    
        $disabledFields = [
            'id_user_client',
        ];
        
        $readonlyFields = [
            'requester_name',
            'requester_email',
        ];
        
        $countries = $countriesEntity->getCountries();
        $tariffcodes = $certificate->certificateTariffcodes;
        $dus = $certificate->certificateDus;
        $containers = $certificate->certificateContainers;
        $containersTypes = $certificate->certificateContainersTypes;
    
        $maxCertificateContainers = CertificateContainers::getMax();
        $maxCertificateContainersTypes = CertificateContainersTypes::getMax();
    
        $allContainerTypesModels = $containerTypesModel::find()->all();
        $currentContainerTypes = [];
        $addContainerTypes = [];
    
        foreach ($containersTypes as $ct) {
            $currentContainerTypes[$ct->id_container_type] = $ct->id_container_type;
        }
        foreach ($allContainerTypesModels as $model) {
            if (in_array($model->id_container_type, $currentContainerTypes)) {
                continue;
            }
        
            $addContainerTypes[$model->id_container_type] = $model->name;
        }
        
        if (\Yii::$app->request->isPost) {
            $post = \Yii::$app->request->post();
            
            
            $transaction = Yii::$app->db->beginTransaction();
            try {
                
                if ($certificate->load($post)) {
                    // force value for a few not required fields
                    $certificate->goods_origin_id_country = $certificate->goods_origin_id_country != "" ? $certificate->goods_origin_id_country : null;
                    $certificate->goods_loading_id_country = $certificate->goods_loading_id_country != "" ? $certificate->goods_loading_id_country : null;
                   
                    if ($certificate->validate() && $certificate->save()) {
    
                        /*CustomHelper::updateRelationalData('certificateGuichet', 'common\models\CertificateGuichet',
                            $certificate, 'id_certificate');
                        CustomHelper::updateRelationalData('certificateTranshipments',
                            'common\models\CertificateTranshipments', $certificate, 'id_certificate');
                        CustomHelper::updateRelationalData('certificateChassis', 'common\models\certificateChassis',
                            $certificate, 'id_certificate');
                        CustomHelper::updateRelationalData('certificateContainers',
                            'common\models\certificateContainers', $certificate, 'id_certificate');
                        CustomHelper::updateRelationalData('certificateTariffcodes',
                            'common\models\certificateTariffcodes', $certificate, 'id_certificate');*/
                        
                        
                        CustomHelper::updateRelationalData('certificateContainers', 'common\models\certificateContainers',
                            $certificate, 'id_certificate');
                        CustomHelper::updateRelationalData('certificateContainersTypes',
                            'common\models\certificateContainersTypes', $certificate, 'id_certificate');
    
                        CustomHelper::updateRelationalData('certificateTariffcodes', 'common\models\certificateTariffcodes',
                            $certificate, 'id_certificate');
    
                        CustomHelper::updateRelationalData('certificateDus', 'common\models\certificateDus', $certificate,
                            'id_certificate', [
                                'folder' => 'dus/',
                                'docModel' => $doc,
                                'relation' => $certificateDus,
                            ]);
                        
                        $id = $certificate->primaryKey;
                        $doc->load($post);
                        $doc->upload_bill_lading = UploadedFile::getInstance($doc, 'upload_bill_lading');
                        $doc->upload_commercial_invoice = UploadedFile::getInstance($doc, 'upload_commercial_invoice');
                        $doc->upload_freight_invoice = UploadedFile::getInstance($doc, 'upload_freight_invoice');
                        $doc->upload_draft_request_signed = UploadedFile::getInstance($doc,'upload_draft_request_signed');
                        $doc->upload_other = UploadedFile::getInstance($doc,'upload_other');
                        
                        if (!empty($doc->upload_bill_lading)) {
                            $docBillLading = new Doc();
                            $fileName = 'docs/doc_' . $id . '_' . $doc->upload_bill_lading->baseName . '_' . time() . '.' . $doc->upload_bill_lading->extension;
                            $fileName = preg_replace('/\s+/', '', $fileName);
                            $docBillLading->path = $fileName;
                            $docBillLading->name = $doc->upload_bill_lading->baseName;
                            $docBillLading->save();
                            $doc->upload_bill_lading->saveAs($fileName);
                            $certificate->id_docs_bill_lading = $docBillLading->id_doc;
                            $certificate->save();
                        }
                        if (!empty($doc->upload_commercial_invoice)) {
                            $docCommercialInvoice = new Doc();
                            $fileName = 'docs/doc_' . $id . '_' . $doc->upload_commercial_invoice->baseName . '_' . time() . '.' . $doc->upload_commercial_invoice->extension;
                            $fileName = preg_replace('/\s+/', '', $fileName);
                            $docCommercialInvoice->path = $fileName;
                            $docCommercialInvoice->name = $doc->upload_commercial_invoice->baseName;
                            $docCommercialInvoice->save();
                            $doc->upload_commercial_invoice->saveAs($fileName);
                            $certificate->id_docs_commercial_invoice = $docCommercialInvoice->id_doc;
                            $certificate->save();
                        }
                        if (!empty($doc->upload_freight_invoice)) {
                            $docFreightInvoice = new Doc();
                            $fileName = 'docs/doc_' . $id . '_' . $doc->upload_freight_invoice->baseName . '_' . time() . '.' . $doc->upload_freight_invoice->extension;
                            $fileName = preg_replace('/\s+/', '', $fileName);
                            $docFreightInvoice->path = $fileName;
                            $docFreightInvoice->name = $doc->upload_freight_invoice->baseName;
                            $docFreightInvoice->save();
                            $doc->upload_freight_invoice->saveAs($fileName);
                            $certificate->id_docs_freight_invoice = $docFreightInvoice->id_doc;
                            $certificate->save();
                        }
                        if (!empty($doc->upload_draft_request_signed)) {
                            $docDraftRequestSigned = new Doc();
                            $fileName = 'docs/doc_' . $id . '_' . $doc->upload_draft_request_signed->baseName . '_' . time() . '.' . $doc->upload_draft_request_signed->extension;
                            $fileName = preg_replace('/\s+/', '', $fileName);
                            $docDraftRequestSigned->path = $fileName;
                            $docDraftRequestSigned->name = $doc->upload_draft_request_signed->baseName;
                            $docDraftRequestSigned->save();
                            $doc->upload_draft_request_signed->saveAs($fileName);
                            $certificate->id_docs_draft_request_signed = $docDraftRequestSigned->id_doc;
                            $certificate->save();
                        }
                        if (!empty($doc->upload_other)) {
                            $docOther = new Doc();
                            $fileName = 'docs/doc_' . $id . '_' . $doc->upload_other->baseName . '_' . time() . '.' . $doc->upload_other->extension;
                            $fileName = preg_replace('/\s+/', '', $fileName);
                            $docOther->path = $fileName;
                            $docOther->name = $doc->upload_other->baseName;
                            $docOther->save();
                            $doc->upload_other->saveAs($fileName);
                            $certificate->id_docs_other = $docOther->id_doc;
                            $certificate->save();
                        }
                        
                        if (empty($doc->upload_bill_lading) || empty($doc->upload_commercial_invoice) || empty($doc->upload_freight_invoice)) {
                            $transaction->rollBack();
                            
                            return $this->render('create', [
                                'certificate' => $certificate,
                                'doc' => $doc,
                                'certificateContainers' => $certificateContainers,
                                'certificateContainersTypes' => $certificateContainersTypes,
                                'certificateTariffcodes' => $certificateTariffcodes,
                                'certificateDus' => $certificateDus,
                                'countries' => $countries,
                                'disabledFields' => $disabledFields,
                                'readonlyFields' => $readonlyFields,
                                'tariffcodes' => $tariffcodes,
                                'dus' => $dus,
                                'containers' => $containers,
                                'containersTypes' => $containersTypes,
                                'maxCertificateContainers' => $maxCertificateContainers,
                                'maxCertificateContainersTypes' => $maxCertificateContainersTypes,
                                'addContainerTypes' => $addContainerTypes,
                            ]);
                        }
                        
                        if ($certificate->validate() && $docBillLading->validate() && $docCommercialInvoice->validate() && $docFreightInvoice->validate()) {
                            $transaction->commit();
                            return $this->redirect(['update', 'id' => $certificate->id_certificate]);
                        }
                    }
                }
            } catch (Exception $e) {
                $transaction->rollBack();
            }
        }
        
        return $this->render('create', [
            'certificate' => $certificate,
            'doc' => $doc,
            'certificateContainers' => $certificateContainers,
            'certificateContainersTypes' => $certificateContainersTypes,
            'certificateTariffcodes' => $certificateTariffcodes,
            'certificateDus' => $certificateDus,
            'countries' => $countries,
            'disabledFields' => $disabledFields,
            'readonlyFields' => $readonlyFields,
            'tariffcodes' => $tariffcodes,
            'dus' => $dus,
            'containers' => $containers,
            'containersTypes' => $containersTypes,
            'maxCertificateContainers' => $maxCertificateContainers,
            'maxCertificateContainersTypes' => $maxCertificateContainersTypes,
            'addContainerTypes' => $addContainerTypes,
        ]);
    }
    
    /**
     * Displays a single Certificate model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     * @throws \yii\web\HttpException
     */
    public function actionView($id)
    {
        $certificate = $this->findModel($id);
        
        $doc = new Doc();
        $countriesEntity = new Country();
        $certificateContainers = new CertificateContainers();
        $certificateContainersTypes = new CertificateContainersTypes();
        $certificateTariffcodes = new CertificateTariffcodes();
        $certificateDus = new CertificateDus();
        $containerTypesModel = new ContainerTypes();
        $countries = $countriesEntity->getCountries();
    
        $tariffcodes = $certificate->certificateTariffcodes;
        $dus = $certificate->certificateDus;
        $containers = $certificate->certificateContainers;
        $containersTypes = $certificate->certificateContainersTypes;
        $maxCertificateContainers = CertificateContainers::getMax();
        $maxCertificateContainersTypes = CertificateContainersTypes::getMax();
        $addContainerTypes = [];
        
        $disabledFields = [
            'docs_bill_ladding',
            'docs_commercial_invoice',
            'docs_freight_invoice',
            'docs_draft_request_signed',
            'upload_bill_lading',
            'upload_commercial_invoice',
            'upload_freight_invoice',
            'upload_draft_request_signed',
            'upload_other',
            'requester_name',
            'requester_email',
            'exporter_name',
            'exporter_phone',
            'exporter_email',
            'exporter_address',
            'exporter_id_country',
            'exporter_id_city',
            'guichet_pr_nr',
            'guichet_statistic_nr',
            'di',
            'importer_name',
            'importer_phone',
            'importer_email',
            'importer_address',
            'importer_vat',
            'importer_id_country',
            'importer_id_city',
            'vessel_name',
            'vessel_voyage_nr',
            'vessel_shipping_line',
            'vessel_incoterm',
            'vessel_bl_nr',
            'vessel_loading_date',
            'forwarding_agent',
            'lading_packages_nr',
            'lading_weight',
            'lading_volume',
            'lading_chassis_nr',
            'lading_40ft_nr',
            'lading_20ft_nr',
            'cost_invoice_currency',
            'cost_invoice_value',
            'cost_fob_currency',
            'cost_fob_value',
            'cost_oceanfreight_currency',
            'cost_oceanfreight_value',
            'cost_baf_currency',
            'cost_baf_value',
            'cost_caf_currency',
            'cost_caf_value',
            'cost_thc_currency',
            'cost_thc_value',
            'cost_insurance_currency',
            'cost_insurance_value',
            'cost_extracharges_currency',
            'cost_extracharges_value',
            'goods_imgcode',
            'goods_origin_id_country',
            'goods_origin_id_harbor',
            'goods_loading_id_country',
            'goods_description',
            'goods_loading_harbor',
            'goods_destination_harbor',
            'goods_loading_date',
            'goods_deliveryestimate_date',
            'id_product_type',
            'CertificateGuichetDi'
        ];
        
        return $this->render('view', [
            'certificate' => $certificate,
            'doc' => $doc,
            'certificateContainers' => $certificateContainers,
            'certificateContainersTypes' => $certificateContainersTypes,
            'certificateTariffcodes' => $certificateTariffcodes,
            'certificateDus' => $certificateDus,
            'disabledFields' => $disabledFields,
            'countries' => $countries,
            'tariffcodes' => $tariffcodes,
            'dus' => $dus,
            'containers' => $containers,
            'containersTypes' => $containersTypes,
            'maxCertificateContainers' => $maxCertificateContainers,
            'maxCertificateContainersTypes' => $maxCertificateContainersTypes,
            'addContainerTypes' => $addContainerTypes,
        ]);
    }
    
    /**
     * Updates an existing Certificate model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     * @throws \yii\web\HttpException
     */
    public function actionUpdate($id)
    {
        $certificate = $this->findModel($id);
        
        $doc = new Doc();
        $countriesEntity = new Country();
        $certificateContainers = new CertificateContainers();
        $certificateContainersTypes = new CertificateContainersTypes();
        $certificateTariffcodes = new CertificateTariffcodes();
        $certificateDus = new CertificateDus();
        $containerTypesModel = new ContainerTypes();
        
        $disabledFields = [
            'id_user_client',
            'requester_name',
            'requester_email',
        ];
        
        $countries = $countriesEntity->getCountries();
        $tariffcodes = $certificate->certificateTariffcodes;
        $dus = $certificate->certificateDus;
        $containers = $certificate->certificateContainers;
        $containersTypes = $certificate->certificateContainersTypes;
        
        if (\Yii::$app->request->isPost) {
            
            $post = \Yii::$app->request->post();
            
            $doc->load($post);
            $doc->upload_bill_lading = UploadedFile::getInstance($doc, 'upload_bill_lading');
            $doc->upload_commercial_invoice = UploadedFile::getInstance($doc, 'upload_commercial_invoice');
            $doc->upload_freight_invoice = UploadedFile::getInstance($doc, 'upload_freight_invoice');
            $doc->upload_draft_request_signed = UploadedFile::getInstance($doc, 'upload_draft_request_signed');
            $doc->upload_other = UploadedFile::getInstance($doc, 'upload_other');
            
            if (!empty($doc->upload_bill_lading)) {
                $docBillLading = $doc->findOne($certificate->id_docs_bill_lading);
                $fileName = 'docs/doc_' . $id . '_' . $doc->upload_bill_lading->baseName . '_' . time() . '.' . $doc->upload_bill_lading->extension;
                $fileName = preg_replace('/\s+/', '', $fileName);
                $docBillLading->path = $fileName;
                $docBillLading->name = $doc->upload_bill_lading->baseName;
                $docBillLading->save();
                $doc->upload_bill_lading->saveAs($fileName);
                $certificate->id_docs_bill_lading = $docBillLading->id_doc;
                $certificate->save();
            }
            if (!empty($doc->upload_commercial_invoice)) {
                $docCommercialInvoice = $doc->findOne($certificate->id_docs_commercial_invoice);
                $fileName = 'docs/doc_' . $id . '_' . $doc->upload_commercial_invoice->baseName . '_' . time() . '.' . $doc->upload_commercial_invoice->extension;
                $fileName = preg_replace('/\s+/', '', $fileName);
                $docCommercialInvoice->path = $fileName;
                $docCommercialInvoice->name = $doc->upload_commercial_invoice->baseName;
                $docCommercialInvoice->save();
                $doc->upload_commercial_invoice->saveAs($fileName);
                $certificate->id_docs_commercial_invoice = $docCommercialInvoice->id_doc;
                $certificate->save();
            }
            if (!empty($doc->upload_freight_invoice)) {
                $docFreightInvoice = $doc->findOne($certificate->id_docs_freight_invoice);
                $fileName = 'docs/doc_' . $id . '_' . $doc->upload_freight_invoice->baseName . '_' . time() . '.' . $doc->upload_freight_invoice->extension;
                $fileName = preg_replace('/\s+/', '', $fileName);
                $docFreightInvoice->path = $fileName;
                $docFreightInvoice->name = $doc->upload_freight_invoice->baseName;
                $docFreightInvoice->save();
                $doc->upload_freight_invoice->saveAs($fileName);
                $certificate->id_docs_freight_invoice = $docFreightInvoice->id_doc;
                $certificate->save();
            }
            if (!empty($doc->upload_draft_request_signed)) {
                $docDraftRequestSigned = $doc->findOne($certificate->id_docs_draft_request_signed);
        
                if (!$docDraftRequestSigned) {
                    $docDraftRequestSigned = new Doc();
                }
        
                $fileName = 'docs/doc_' . $id . '_' . $doc->upload_draft_request_signed->baseName . '_' . time() . '.' . $doc->upload_draft_request_signed->extension;
                $fileName = preg_replace('/\s+/', '', $fileName);
                $docDraftRequestSigned->path = $fileName;
                $docDraftRequestSigned->name = $doc->upload_draft_request_signed->baseName;
                $docDraftRequestSigned->save();
                $doc->upload_draft_request_signed->saveAs($fileName);
                $certificate->id_docs_draft_request_signed = $docDraftRequestSigned->id_doc;
                $certificate->save();
            }
            if (!empty($doc->upload_other)) {
                $docOther = $doc->findOne($certificate->id_docs_other);
        
                if (!$docOther) {
                    $docOther = new Doc();
                }
        
                $fileName = 'docs/doc_' . $id . '_' . $doc->upload_other->baseName . '_' . time() . '.' . $doc->upload_other->extension;
                $fileName = preg_replace('/\s+/', '', $fileName);
                $docOther->path = $fileName;
                $docOther->name = $doc->upload_other->baseName;
                $docOther->save();
                $doc->upload_other->saveAs($fileName);
                $certificate->id_docs_other = $docOther->id_doc;
                $certificate->save();
            }
            
            if ($certificate->load($post)) {
                if ($certificate->save()) {
                    if (!empty($doc->upload_draft_request_signed) && $certificate->last_status == CertificateStatus::STATUS_DRAFT) {
                        Certificate::changeStatus($certificate->primaryKey, null, false, false, false,
                            CertificateStatus::STATUS_PROCESS);
                    }
                    
                    CustomHelper::updateRelationalData('certificateContainers', 'common\models\certificateContainers',
                        $certificate, 'id_certificate');
                    CustomHelper::updateRelationalData('certificateContainersTypes',
                        'common\models\certificateContainersTypes', $certificate, 'id_certificate');
                    CustomHelper::updateRelationalData('certificateTariffcodes', 'common\models\certificateTariffcodes',
                        $certificate, 'id_certificate');
                    
                    //TODO add a behavior to Certificate_Dus::model() that delete respective path file on delete
                    CustomHelper::updateRelationalData('certificateDus', 'common\models\certificateDus', $certificate,
                        'id_certificate', [
                            'folder' => 'dus/',
                            'docModel' => $doc,
                            'relation' => $certificateDus,
                        ]);
                    return $this->refresh();
                }
            }
        }
        
        $maxCertificateContainers = CertificateContainers::getMax();
        $maxCertificateContainersTypes = CertificateContainersTypes::getMax();
        
        $allContainerTypesModels = $containerTypesModel::find()->all();
        $currentContainerTypes = [];
        $addContainerTypes = [];
        
        foreach ($containersTypes as $ct) {
            $currentContainerTypes[$ct->id_container_type] = $ct->id_container_type;
        }
        foreach ($allContainerTypesModels as $model) {
            if (in_array($model->id_container_type, $currentContainerTypes)) {
                continue;
            }
            
            $addContainerTypes[$model->id_container_type] = $model->name;
        }
        
        return $this->render('update', [
            'certificate' => $certificate,
            'doc' => $doc,
            'certificateContainers' => $certificateContainers,
            'certificateContainersTypes' => $certificateContainersTypes,
            'certificateTariffcodes' => $certificateTariffcodes,
            'certificateDus' => $certificateDus,
            'countries' => $countries,
            'disabledFields' => $disabledFields,
            'tariffcodes' => $tariffcodes,
            'dus' => $dus,
            'containers' => $containers,
            'containersTypes' => $containersTypes,
            'maxCertificateContainers' => $maxCertificateContainers,
            'maxCertificateContainersTypes' => $maxCertificateContainersTypes,
            'addContainerTypes' => $addContainerTypes,
        ]);
    }
    
    /**
     * List an existing certificate history.
     *
     * @param integer $id
     *
     * @return mixed
     * @throws \yii\web\HttpException
     */
    public function actionHistory($id)
    {
        // get certificate
        $certificate = $this->findModel($id);
        
        // get certificate history
        $certificateHistoryProvider = CertificateStatus::getStatusProvider($certificate->primaryKey);
        
        return $this->render('history', [
            'certificate' => $certificate,
            'certificateHistoryProvider' => $certificateHistoryProvider,
        ]);
    }
    
    /**
     * List an existing certificate documents.
     *
     * @param integer $id
     *
     * @return mixed
     * @throws \yii\web\HttpException
     */
    public function actionDocuments($id)
    {
        // get certificate
        $certificate = $this->findModel($id);
        
        // map certificate documents
        $certificateDocuments = [];
        if (!empty($certificate->docsBillLading))
            $certificateDocuments['Bill of Lading'] = $certificate->docsBillLading;
        
        if (!empty($certificate->docsCommercialInvoice))
            $certificateDocuments['Commercial Invoice'] = $certificate->docsCommercialInvoice;
    
        if (!empty($certificate->docsDraftRequestSigned))
            $certificateDocuments['Form file signed and stamped'] = $certificate->docsDraftRequestSigned;
    
        if (!empty($certificate->docsSigaDraftRequest))
            $certificateDocuments['Siga Draft Request'] = $certificate->docsSigaDraftRequest;
    
        if (!empty($certificate->docsRejectedDraftValidation))
            $certificateDocuments['Rejected in Draft Validation'] = $certificate->docsRejectedDraftValidation;
    
        if (!empty($certificate->docsOther))
            $certificateDocuments['Other File'] = $certificate->docsOther;
    
        if (!empty($certificate->docsProofOfPayment))
            $certificateDocuments['Proof of Payment'] = $certificate->docsProofOfPayment;
        
        if (!empty($certificate->docsFreightInvoice))
            $certificateDocuments['Freight Invoice'] = $certificate->docsFreightInvoice;
        
        if (!empty($certificate->invoicesCertificates->invoice->doc->path))
            $certificateDocuments['Invoice Angdocs'] = $certificate->invoicesCertificates->invoice->doc;
    
        if (!empty($certificate->docsValidatedFile))
            $certificateDocuments['Validated File'] = $certificate->docsValidatedFile;
    
        if (!empty($certificate->docsCertificate))
            $certificateDocuments['Official Certificate'] = $certificate->docsCertificate;
    
        $rejecteds = [];
        if (!empty($certificate->id_docs_rejected_draft_validation))
            $rejecteds = array_filter(explode(';',$certificate->id_docs_rejected_draft_validation));
        
        if(count($rejecteds)>0){
            $doc = new Doc();
            foreach ($rejecteds as &$rejected)
                $rejected = $doc->find()->where('id_doc = :doc',[':doc'=>$rejected])->one();
        }
    
    
        return $this->render('documents', [
            'certificate' => $certificate,
            'certificateDocuments' => $certificateDocuments,
            'rejectedFiles' => $rejecteds
        ]);
    }
    
    /**
     * Clones a Certificate model.
     *
     * @param integer $id
     *
     * @return mixed
     * @throws \yii\web\HttpException
     */
    public function actionClone($id)
    {
        $baseCertificate = $this->findModel($id);
        $certificate = new Certificate($baseCertificate->getAttributes());
        $certificate->id_certificate = null;
        $certificate->id_docs_bill_lading = null;
        $certificate->id_docs_commercial_invoice = null;
        $certificate->id_docs_freight_invoice = null;
        $certificate->id_docs_draft_request_signed = null;
        $certificate->id_docs_certificate = null;
        $certificate->id_user_maker = null;
        $certificate->id_user_invoicer = null;
        $certificate->last_status = 1;
        $certificate->save();
        
        return $this->redirect(["update", "id" => $certificate->primaryKey]);
    }
    
    /**
     * Print an existing certificate history.
     *
     * @param integer $id
     *
     * @return mixed
     * @throws \yii\web\HttpException
     */
    public function actionPrint($id)
    {
        try {
            $certificate = $this->findModel($id);
            
            if ($certificate) {
                //User Requester
                $userEntity = new User();
                $requester = $userEntity->find()
                    ->where("name = '{$certificate->requester_name}'")
                    ->where("email = '{$certificate->requester_email}'")
                    ->one();
                
                //Country
                $countryEntity = new Country();
                $certificate->importer_id_country = [
                    'id' => $certificate->importer_id_country,
                    'name' => $countryEntity->getCountryNameByCca2($certificate->importer_id_country)
                ];
                $certificate->exporter_id_country = [
                    'id' => $certificate->importer_id_country,
                    'name' => $countryEntity->getCountryNameByCca2($certificate->exporter_id_country)
                ];
    
                
    
                //Certificate Containers Types
                $certificateContainersTypesEntity = new CertificateContainersTypes();
                $certificateContainersTypes = $certificateContainersTypesEntity->find()
                    ->where("id_certificate = '{$certificate->id_certificate}'")
                    ->all();
    
                $certificateContainers = [];
                //Certificate Containers
                $certificateContainerEntity = new CertificateContainers();
                foreach ($certificateContainersTypes as $containerType) {
                    $containers = $certificateContainerEntity->find()
                        ->where("id_container_type = '{$containerType->id_certificate_container_type}'")
                        ->all();
                    if((is_array($containers) || is_object($containers) || is_iterable($containers) || $containers instanceof Countable) && !empty($containers)){
                        foreach ($containers as $container) {
                            $certificateContainers[$container->id_container_type] = empty($certificateContainers[$container->id_container_type])? '': $certificateContainers[$container->id_container_type];
                            $certificateContainers[$container->id_container_type] .= $container->nr.'; ';
                        }
                    }
                }
                
                //Certificate Products
                $certificateProductsEntity = new CertificateTariffcodes();
                $certificateProducts = $certificateProductsEntity->find()
                    ->where("id_certificate = '{$certificate->id_certificate}'")
                    ->all();
    
                //Certificate Dus
                $certificateDusEntity = new CertificateDus();
                $certificateDus = $certificateDusEntity->find()
                    ->where("id_certificate = '{$certificate->id_certificate}'")
                    ->all();
                
                
            }
        } catch (\Exception $e) {
            throw new \Exception(Yii::t('frontend', 'Certificate not found') . ' ' . $e->getMessage());
        }
        
            return $this->render('print', [
                'certificate' => $certificate,
                'certificateContainers' => $certificateContainers,
                'certificateContainersTypes' => $certificateContainersTypes,
                'certificateProducts' => $certificateProducts,
                'certificateDus' => $certificateDus,
                'requester' => $requester
            ]);
    }
    
    /**
     * Deletes an existing Certificate model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param $id
     *
     * @return \yii\web\Response
     * @throws \Throwable
     */
    public function actionDelete($id)
    {
        $user = Yii::$app->user->identity;
        if ($user->type == User::TYPE_MANAGER) {
            try {
                $this->findModel($id)->delete();
            } catch (\Exception $e) {
                $msg = (isset($e->errorInfo[2])) ? $e->errorInfo[2] : $e->getMessage();
                \Yii::$app->getSession()->addFlash('error', $msg);
                
                return $this->redirect(Url::previous());
            }
            
            
            // TODO: improve detection
            $isPivot = strstr('$id_certificate', ',');
            if ($isPivot == true) {
                return $this->redirect(Url::previous());
            } elseif (isset(\Yii::$app->session['__crudReturnUrl']) && \Yii::$app->session['__crudReturnUrl'] != '/') {
                Url::remember(null);
                $url = \Yii::$app->session['__crudReturnUrl'];
                \Yii::$app->session['__crudReturnUrl'] = null;
                
                return $this->redirect($url);
            } else {
                return $this->redirect(['/']);
            }
        } else {
            throw new HttpException(404, 'The requested page does not exist.');
        }
    }
    
    /**
     * Finds the Certificate model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @throws HttpException if the model cannot be found
     *
     * @param integer $id
     *
     * @return Certificate the loaded model
     */
    protected function findModel($id)
    {
        if (($model = Certificate::findOne($id)) !== null) {
            return $model;
        } else {
            throw new HttpException(404, 'The requested page does not exist.');
        }
    }
    
    public static function string_slice($input, $slice)
    {
        $arg = explode(':', $slice);
        $start = intval($arg[0]);
        if ($start < 0) {
            $start += strlen($input);
        }
        if (count($arg) === 1) {
            return substr($input, $start, 1);
        }
        if (trim($arg[1]) === '') {
            return substr($input, $start);
        }
        $end = intval($arg[1]);
        if ($end < 0) {
            $end += strlen($input);
        }
        
        return substr($input, $start, $end - $start);
    }
    
    public function actionCheckTariffcode($value)
    {
        
        $tariffClass = self::string_slice($value, ':2');
        $tariffCategory = self::string_slice($value, '2:4');
        $tariffProduct = self::string_slice($value, '4:');
        
        $hasTariffClass = is_numeric($tariffClass);
        $hasTariffCategory = is_numeric($tariffCategory);
        $hasTariffProduct = is_numeric($tariffProduct);
        
        $class = $hasTariffClass ? TariffcodesClass::find()->where(['code' => $tariffClass])->one() : null;
        $category = $hasTariffCategory ? TariffcodesCategory::find()->where([
            'code_class' => $tariffClass,
            'code' => $tariffCategory
        ])->one() : null;
        $product = $hasTariffProduct ? TariffcodesProduct::find()->where([
            'code_class' => $tariffClass,
            'code_category' => $tariffCategory,
            'code' => $tariffProduct
        ])->one() : null;
        
        $hasCategory = $hasProduct = true;
        
        $hasClass = !is_null($class) ? true : false;
        if ($hasTariffCategory) {
            $hasCategory = !is_null($category) ? true : false;
        }
        
        if ($hasTariffProduct) {
            $hasProduct = !is_null($product) ? true : false;
        }
        
        
        $resultsJSON['tariffcodes'] = [
            'id_class' => $class ? $class->id_tariffcodes_class : null,
            'id_category' => $category ? $category->id_tariffcodes_category : null,
            'id_product' => $product ? $product->id_tariffcodes_product : null
        ];
        
        $resultTariffcodeClass = TariffcodesClass::find()->all();
        $hasResultTariffcodeClass = !empty($resultTariffcodeClass);
        if ($hasResultTariffcodeClass) {
            foreach ($resultTariffcodeClass as $result) {
                $resultsJSON['tariffcodeClass'][] = [
                    'id_tariffcodes_class' => $result->id_tariffcodes_class,
                    'description' => $result->description_pt,
                    'code' => $result->code,
                ];
            }
        } else {
            $resultsJSON = $this->error(Yii::t('frontend', 'Tariff Code Class incorrect'));
        }
        
        if ($hasTariffClass and !empty($class)) {
            $resultTariffcodeCategory = TariffcodesCategory::find()->where(['code_class' => $class->code])->all();
            $hasResultTariffcodeCategory = !empty($resultTariffcodeCategory);
            if ($hasResultTariffcodeCategory) {
                foreach ($resultTariffcodeCategory as $result) {
                    $resultsJSON['tariffcodeCategory'][] = [
                        'id_tariffcodes_category' => $result->id_tariffcodes_category,
                        'description' => $result->description_pt,
                        'code' => $result->code,
                    ];
                }
            } else {
                $resultsJSON = $this->error(Yii::t('frontend', 'Tariff Code Category incorrect'));
            }
        }
        
        if ($hasTariffCategory and !empty($category)) {
            $resultTariffcodeProduct = TariffcodesProduct::find()->where([
                'code_class' => $class->code,
                'code_category' => $category->code
            ])->all();
            $hasResultTariffcodeProduct = !empty($resultTariffcodeProduct);
            if ($hasResultTariffcodeProduct) {
                foreach ($resultTariffcodeProduct as $result) {
                    $resultsJSON['tariffcodeProduct'][] = [
                        'id_tariffcodes_product' => $result->id_tariffcodes_product,
                        'description' => $result->description_pt,
                        'code' => $result->code,
                    ];
                }
            } else {
                $resultsJSON = $this->error(Yii::t('frontend', 'Tariff Code Product incorrect'));
            }
        }
        
        if (!$hasClass) {
            $resultsJSON = $this->error(Yii::t('frontend', 'Tariff Code Class incorrect'));
        }
        
        if (!$hasCategory) {
            $resultsJSON = $this->error(Yii::t('frontend', 'Tariff Code Category incorrect'));
        }
        
        if (!$hasProduct) {
            $resultsJSON = $this->error(Yii::t('frontend', 'Tariff Code Product incorrect'));
        }
        
        
        echo JSON::encode($resultsJSON);
        exit;
    }
    
    private function error($mensagem)
    {
        return ['error' => $mensagem];
    }
    
    public function actionSearchCategory($class)
    {
        $results = \common\models\TariffcodesCategory::find()->where(['id_class' => $class])->all();
        $output = [];
        $resultsJSON[] = [
            'id_tariffcodes_category' => null,
            'description' => Yii::t('frontend', 'Pick a category'),
            'code' => null,
        ];
        if (!empty($results)) {
            foreach ($results as $result) {
                $resultsJSON[] = [
                    'id_tariffcodes_category' => $result->id_tariffcodes_category,
                    'description' => $result->description_pt,
                    'code' => $result->code,
                ];
            }
        }
        echo JSON::encode($resultsJSON);
        exit;
    }
    
    public function actionSearchProduct($category)
    {
        $results = \common\models\TariffcodesProduct::find()->where(['id_category' => $category])->all();
        $output = [];
        $resultsJSON[] = [
            'id_tariffcodes_category' => null,
            'description' => Yii::t('frontend', 'Pick a product'),
            'code' => null,
        ];
        
        if (!empty($results)) {
            foreach ($results as $result) {
                $resultsJSON[] = [
                    'id_tariffcodes_product' => $result->id_tariffcodes_product,
                    'description' => $result->description_pt,
                    'code' => $result->code,
                ];
            }
        }
        echo JSON::encode($resultsJSON);
        exit;
    }
    
    public function actionTest()
    {
        $htmlToPdfComponent = new HtmlToPDF();
        
        
        //return $htmlToPdfComponent->convertHtmlToPDF("http://google.com", [], false, true);
        return $htmlToPdfComponent->convertHtmlToPDF(Url::toRoute([
            "certificates/application-form"
        ], true), [], false, true);
    }
    
    public function actionApplicationForm($id = null)
    {
        $this->layout = "documents";
        /*
          $hasherArray = [
          "id" => 8,
          "uid" => Yii::$app->user->identity->id,
          "uts" => Yii::$app->user->identity->created_at,
          ];

          $hash = urlencode(base64_encode(json_encode($hasherArray)));
         */
        
        //$hash = "eyJpZCI6OCwidWlkIjoxLCJ1dHMiOiIyMDE3LTEyLTA4IDAwOjAwOjAwIn0%3D";
        $hash = Yii::$app->request->get('hash');
        $render = Yii::$app->request->get('render');
        $hashed = json_decode(base64_decode(urldecode($hash)));
        
        if ($render != 'html') {
            if (is_object($hashed) * !empty($hashed->id) * !empty($hashed->uid) * !empty($hashed->uts) == false) {
                return $this->goHome();
            }
            
            $id = $hashed->id;
            $userId = $hashed->uid;
            $userTs = $hashed->uts;
            
            if (Yii::$app->user->isGuest) {
                $user = User::find()->where([
                    'id' => $userId,
                    'created_at' => $userTs,
                ])->one();
                if ($user) {
                    Yii::$app->user->identity = $user;
                } else {
                    return $this->goHome();
                }
            }
        }
        $certificate = $this->findModel($id);
        if ($certificate) {
            
            if (!is_null($render)) {
                return $this->render('applicationForm', [
                    'certificate' => $certificate
                ]);
            } else {
                $htmlToPdfComponent = new HtmlToPDF();
                
                return $htmlToPdfComponent->convertHtmlToPDF(Url::toRoute([
                    'certificates/application-form',
                    'hash' => $hash,
                    'render' => 'pdf'
                ], true), [], false);
            }
        }
    }
    
    public function actionExportDu($id_certificate){
        $files = [];
        if(is_numeric($id_certificate) && $id_certificate>0){
            $certificateDusEntity = new CertificateDus();
            
            $certificatesDus = $certificateDusEntity
                ->find()
                ->where('id_certificate=:idCertificate',[':idCertificate'=>$id_certificate])
                ->all();
            
            foreach ($certificatesDus as $item){
                if(!empty($item['path'])){
                    $files[]=$item['path'];
                }
            }
            if(count($files)>0){
                $zipname = 'exports_dus_'.date('Ymd').'_'.time();
                
                $zipname = "exports_dus/{$zipname}.zip";
                $zip = new \ZipArchive();
                $zip->open($zipname, \ZipArchive::CREATE);
                foreach ($files as $file) {
                    $zip->addFile($file);
                }
                $zip->close();
        
                header('Content-Type: application/zip');
                header('Content-disposition: attachment; filename='.$zipname);
                header('Content-Length: ' . filesize($zipname));
                readfile($zipname);
            }
        }
        $this->goHome();
    }
    
    public function actionExportHs($id){
		/*
		$date = gmdate("dmYHis");
		
		$nome_arquivo = "HS-product-{$date}";
		
		header("Content-type: application/vnd.ms-excel");
		header("Content-type: application/force-download");
		header("Content-Disposition: attachment; filename={$nome_arquivo}.xls");
		header("Pragma: no-cache");
		 

		$html = '';
		$html .= "<body>";
		$html .= "<table>";
		$html .= "<tbody>";
		
		$certificate = $this->findModel($id);
		$exportArray = $this->makeArrayTariffCode($certificate);
		
		$head = 0;
		foreach ($exportArray as $tariffCodes){
			$html .= "<tr>";
			$head++;
			foreach ($tariffCodes as $tariffCode){
				
				$tagBegin = $head === 1 ? '<strong>':'';
				$tagEnd = $head === 1 ? '</strong>':'';
				
				$html .= "<td>{$tagBegin}{$tariffCode}{$tagEnd}</td>";
			}
			$html .= "</tr>";
		}
		$html .= "</body>";
		echo $html;
		exit;
		*/
		
        $certificate = $this->findModel($id);
        $date = gmdate("dmYHis");
        $filename = "hs-products/hs-products-$id-$date.xlsx";
        
        try{
           
            $exportArray = $this->makeArrayTariffCode($certificate);
			
			$spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            $row = 0;
            foreach ($exportArray as $tariffCodes){
                $row++;
                $column = 0;
                foreach ($tariffCodes as $tariffCode){
                    $column++;
                    $sheet->setCellValueByColumnAndRow($column,$row,$tariffCode);
                }
            }
    
            $writer = new Xlsx($spreadsheet);
            $writer->save($filename);
    
            $this->makeSpreadSheetsHeaders($filename);
            
        }catch(\Exception $e){
            echo $e->getMessage();
        }
        exit;
    }
    
    private function makeSpreadSheetsHeaders($filename){
        /*
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-disposition: attachment; filename='.$filename);
        header('Content-Length: ' . filesize($filename));
        readfile($filename);
        */
        
        $now = gmdate("D, d M Y H:i:s");
        
        // disable caching
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");
        
        // force download
        header("Content-Type: application/excel");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Type: application/x-download");
        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        
        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
        header("Pragma: public");
        readfile($filename);
        
    }
    
    private function makeArrayTariffCode(Certificate $certificate){
    
        $i=0;
        $exportArray[] = [
            'row_num' => Yii::t('frontend', 'RowNum'),
            'code' => Yii::t('frontend', 'HSCode'),
            'description' => Yii::t('frontend', 'Description'),
            'country' => Yii::t('frontend', 'Country'),
            'quantity' => Yii::t('frontend', 'Quantity'),
            'value' => Yii::t('frontend', 'Value'),
            'weight' => Yii::t('frontend', 'Weight (Kg)')
        ];
        
        foreach ($certificate->getCertificateTariffcodes()->all() as $tariff){
            $i++;
            $country = new Country();
            
            array_push($exportArray, [
                'row_num' => $i,
                'code' => $tariff->code,
                'description' => $tariff->description,
                'country' => !empty($tariff->id_country) ? $country->getCountryNameByCca2($tariff->id_country):'',
                'quantity' => $tariff->qty,
                'value' => $tariff->value,
                'weight' => $tariff->weight
            ]);
        }
        
        return $exportArray;
    }
}
