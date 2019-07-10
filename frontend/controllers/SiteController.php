<?php

namespace frontend\controllers;

use common\controllers\Controller;
use common\models\Certificate;
use common\models\User;
use common\models\Invoices;
use common\models\Doc;
use lajax\translatemanager\helpers\Language as Lx;
use Yii;
use yii\base\InvalidParamException;
use yii\db\Query;
use yii\web\BadRequestHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\UserForm;
use frontend\models\ContactForm;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends Controller {
	
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
				'only' => ['logout', 'signup', 'contacts'],
				'rules' => [
					[
						'actions' => ['request-password-reset', 'reset-password', 'signup'],
						'allow' => true,
						'roles' => ['?'],
					],
					[
						'actions' => ['logout', 'contacts'],
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [//'logout' => ['post'],
				],
			],
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public function actions() {
		return [
			'error' => [
				'class' => 'yii\web\ErrorAction',
			],
		];
	}
	
	/**
	 * Displays homepage.
	 *
	 * @param int $perPage
	 * @param string $date
	 * @param null $search
	 *
	 * @param bool $changeStatus
	 * @param null $idCertificate
	 * @param bool $description
	 *
	 * @param bool $validate
	 * @param bool $validated
	 *
	 * @param null $price
	 * @param null $city
	 *
	 * @return mixed
	 */
	public function actionIndex($perPage = Certificate::PAGE_SIZE, $date = Certificate::DATE_ALL, $search = null, $changeStatus = false, $idCertificate = null, $description = false, $validate = false, $validated = true, $price = null, $city = null, $status = null, $reject = null, $page = null) {
	    $post = Yii::$app->request->post();
        
        $hasFilter = !(
            empty($city) &&
            empty($city) &&
            empty($city));
        
        if($hasFilter) {
            $page = 1;
        }
        
		$typeFormUpload = !empty($post['type-form-upload']) ? $post['type-form-upload'] : false;
  
		$enclosurePageRules = is_null(Yii::$app->session->get('page') ) || $page != 'null' || !is_numeric(Yii::$app->session->get('page'));
		if($enclosurePageRules) {
		    $isNewValueByPagination = !is_null($page) && is_numeric($page) && $page>0;
		    
            if ($isNewValueByPagination) {
                Yii::$app->session->set('page', $page);
            }
            
            if(is_null(Yii::$app->session->get('page')))
                Yii::$app->session->set('page', 1);
        }
        $page = Yii::$app->session->get('page');
		
		if (Yii::$app->user->isGuest)
			return $this->redirect(['site/login']);
   
		// change certificate status?
        $changeStatusByrequestAjax = $changeStatus  && empty($typeFormUpload) && Yii::$app->request->isAjax;
		if ($changeStatusByrequestAjax){
            
            $get = Yii::$app->getRequest()->getQueryParams();
            $hasFile = isset($get['file']) && is_array($get['file'] ) && !empty($get['file'] );
            if($hasFile){
                foreach ($get['file'] as $key => $file){
                    $doc = Doc::findOne($key);
        
                    //Documents to print
                }
            }
            
			Certificate::changeStatus($idCertificate, $description, $validate, $validated, $price);
            
            header("Location:/site/index?page={$page}&per-page={$perPage}");
            exit();
        }
        
		// Official Certificate
		if ($typeFormUpload == "certificate") {
			$idCertificate = $post['id-certificates'];
			$description = !empty($post['invoice-description']) ?? '';
			$result = Certificate::uploadCertificate($idCertificate);
			if($result == true) {
				Certificate::changeStatus($idCertificate, $description, 1, 1);
                $hasSubmit = true;
			}
		}
        
        
        // one/multi certificate invoice generated
        $isTypeFormInvoice = $typeFormUpload == "invoice";
		if($isTypeFormInvoice)
            Certificate::generateInvoice();
        
        $isTypeFormDraft = $typeFormUpload == "draft";
		if ($isTypeFormDraft)
			Certificate::changeStatus($post['idCertificate'], $post['description'], 1, 1, null);
		
		$isTypeFormSigaDraft = $typeFormUpload == "siga-draft-file";
		if ($isTypeFormSigaDraft)
			Certificate::generateDraftSiga();
		
		$isTypeFormDraft = $typeFormUpload == "draft-validation";
		if ($isTypeFormDraft)
			Certificate::generateDraftValidation();
  
		$isTypeFormValidated = $typeFormUpload == "validated-file";
        if ($isTypeFormValidated)
            Certificate::generateValidatedFile();
        
        $isTypeFormProve = $typeFormUpload == "prove-payment";
        if ($isTypeFormProve)
		    Certificate::generateProvePayment();
        
        if(Yii::$app->request->isPost){
            header("Location:/site/index?page={$page}&per-page={$perPage}");
            exit();
        }else{
            // prepare filters
            $filters = [
                'page' => $page,
                'perPage' => $perPage,
                'search' => $search,
                'date' => $date,
                'city' => $city,
                'status' => $status,
                'reject' => $reject
            ];
            
            // get users provider
            $certificatesProvider = Certificate::provider($perPage, $search, $date,[],false, $city, $status, $reject,$page);
            return $this->render('index', [
                'certificatesProvider' => $certificatesProvider,
                'filters' => $filters,
                'perPage' => $perPage,
                'page' => $page
            ]);
        }
	}
	
	/**
	 * Logs in a user.
	 *
	 * @return mixed
	 */
	public function actionLogin() {
		if (!Yii::$app->user->isGuest) {
			return $this->goHome();
		}
		
		$loginForm = new LoginForm();
		if ($loginForm->load(Yii::$app->request->post()) && $loginForm->login()) {
			if (Yii::$app->user->identity->type == User::TYPE_MANAGER) {
				Yii::$app->session->set('frontendTranslation_EnableTranslate', 1);
			}
			
			return $this->goHome();
		}
		
		return $this->render('login', [
			'loginForm' => $loginForm,
		]);
	}
	
	/**
	 * Logs out the current user.
	 *
	 * @return mixed
	 */
	public function actionLogout() {
		Yii::$app->user->logout();
		
		return $this->goHome();
	}
	
	/**
	 * Signs user up.
	 *
	 * @return mixed
	 * @throws \yii\base\Exception
	 */
	public function actionSignup() {
		$userForm = new UserForm();
		if ($userForm->load(Yii::$app->request->post())) {
			if ($user = $userForm->create() && $userForm->sendEmail($userForm::EMAIL_TYPE_SIGNUP)) {
				/*if (Yii::$app->getUser()->login($user)) {
					return $this->goHome();
				}*/
				
				// show a successful message.
				Yii::$app->getSession()->setFlash('success', [
					'status' => 'success',
					'message' => Lx::t('frontend', 'Account successfully registered. We will validate your account. After validation, an email will be sent to your email inbox with the credentials.')
				]);
				
				return $this->goHome();
			}
		}
		
		return $this->render('signup', [
			'userForm' => $userForm,
		]);
	}
	
	/**
	 * Requests password reset.
	 *
	 * @return mixed
	 */
	public function actionRequestPasswordReset() {
		$model = new PasswordResetRequestForm();
		if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			if ($model->sendEmail()) {
				Yii::$app->getSession()->setFlash('success', [
					'status' => 'success',
					'message' => Lx::t('frontend', 'Check your email for further instructions.')
				]);
				
				return $this->goHome();
			} else {
				Yii::$app->getSession()->setFlash('success', [
					'status' => 'error',
					'message' => Lx::t('frontend', 'Sorry, we are unable to reset password for the provided email address.')
				]);
			}
		}
		
		return $this->render('requestPasswordResetToken', [
			'model' => $model,
		]);
	}
	
	/**
	 * Resets password.
	 *
	 * @param string $token
	 *
	 * @return mixed
	 * @throws BadRequestHttpException
	 * @throws \yii\base\Exception
	 */
	public function actionResetPassword($token) {
		try {
			$model = new ResetPasswordForm($token);
		} catch (InvalidParamException $e) {
			throw new BadRequestHttpException($e->getMessage());
		}
		
		if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
			Yii::$app->getSession()->setFlash('success', [
				'status' => 'success',
				'message' => Lx::t('frontend', 'New password saved.')
			]);
			
			return $this->goHome();
		}
		
		return $this->render('resetPassword', [
			'model' => $model,
		]);
	}
	
	/**
	 * Displays contact page.
	 *
	 * @return mixed
	 */
	public function actionContacts() {
		$contactForm = new ContactForm();
		if ($contactForm->load(Yii::$app->request->post()) && $contactForm->validate()) {
			if ($contactForm->sendEmail(Yii::$app->params['contactEmail'])) {
				Yii::$app->getSession()->setFlash('success', [
					'status' => 'success',
					'message' => Lx::t('frontend', 'Thank you for contacting us. We will respond to you as soon as possible.')
				]);
			} else {
				Yii::$app->getSession()->setFlash('success', [
					'status' => 'error',
					'message' => Lx::t('frontend', 'There was an error sending your message.')
				]);
			}
			
			return $this->refresh();
		} else {
			return $this->render('contacts', [
				'contactForm' => $contactForm,
			]);
		}
	}
    
    public function actionGetInvoiceInformations($invoiceNumber){
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if( ($invoiceNumber) && $invoiceNumber>0) {
            $informations = [
                'lading_packages_nr' => 0,
                'lading_weight' => 0,
                'lading_volume' => 0
            ];
            
            $query = new Query();
            $certificates = $query->select([
                'lading_packages_nr',
                'lading_weight',
                'lading_volume'
            ])
                ->from('invoices')
                ->join(	'INNER JOIN',
                    'invoices_certificates',
                    'invoices_certificates.id_invoice = invoices.id_invoice')
                ->join(	'INNER JOIN',
                    'certificate',
                    'invoices_certificates.id_certificate=certificate.id_certificate')
                ->where('invoices.nr=:invoice_number',[':invoice_number'=>$invoiceNumber])
                ->all();
            if(count($certificates)>0) {
                foreach ($certificates as $certificate) {
                    $informations['lading_packages_nr'] += $certificate['lading_packages_nr'];
                    $informations['lading_weight'] += $certificate['lading_weight'];
                    $informations['lading_volume'] += $certificate['lading_volume'];
                }
                
                return $informations;
            }
        }
        
        return ['message'=>'Not Found'];
    }
    
    public function actionGetCertificateInformations(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $queryParams = Yii::$app->getRequest()->getQueryParams();
        
		$hasCertificate = isset($queryParams['certificates']) && !empty($queryParams['certificates']);
		
		//var_dump($queryParams['certificates']);die;
        
        if($hasCertificate) {
            $informations = [
                'lading_packages_nr' => count($queryParams['certificates']),
                'lading_weight' => 0,
                'lading_volume' => 0
            ];
            $array_certificate = implode(',',$queryParams['certificates']);
            
            $query = new Query();
            $certificates = $query->select([
                'SUM(weight) as lading_weight',
                'SUM(volume) as lading_volume'
            ])
            ->from('certificate_container_type')
            ->where('id_certificate IN ('.$array_certificate.')')
            ->one();
            
            if(!empty($certificates))
                $informations = array_merge($informations,$certificates);
            
            return $informations;
        }
        
        return ['message'=>'Not Found'];
    }

	public function actionGetDocuments($certificateID){
		Yii::$app->response->format = Response::FORMAT_JSON;
		
	    if(is_numeric($certificateID) && $certificateID>0) {
			// get certificate

			$certificate = Certificate::findOne($certificateID);
			
			if($certificate){
				// map certificate documents
				$certificateDocuments = [];
				if (!empty($certificate->docsBillLading))
					$certificateDocuments['Bill of Lading'] = $certificate->docsBillLading;
				
				if (!empty($certificate->docsCommercialInvoice))
					$certificateDocuments['Commercial Invoice'] = $certificate->docsCommercialInvoice;
			
				if (!empty($certificate->docsDraftRequestSigned))
					$certificateDocuments['Draft Request Signed'] = $certificate->docsDraftRequestSigned;
			
				if (!empty($certificate->docsSigaDrafRequest))
					$certificateDocuments['Siga Draft Request'] = $certificate->docsSigaDrafRequest;
			
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
                $rejected = [];
				if (!empty($certificate->id_docs_rejected_draft_validation))
					$rejecteds = array_filter(explode(';',$certificate->id_docs_rejected_draft_validation));
				
				if(count($rejecteds)>0){
					$doc = new Doc();
					foreach ($rejecteds as &$rejected)
						$rejected = $doc->find()->where('id_doc = :doc',[':doc'=>$rejected])->one();
				}
				
				return [
					'certificate' => $certificateDocuments,
					'rejecteds' => $rejected
				];
			}
        }

        return ['message'=>'Not Found'];
	}
}
