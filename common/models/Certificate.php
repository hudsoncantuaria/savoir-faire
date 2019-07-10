<?php

namespace common\models;

use \common\models\base\Certificate as BaseCertificate;
use lajax\translatemanager\helpers\Language as Lx;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * This is the model class for table "certificate".
 */
class Certificate extends BaseCertificate {
	
	// VESSEL_INCOTERM
	const VESSEL_INCOTERM = [
		1 => 'CFR',
		2 => 'CIF',
		3 => 'CPT',
		4 => 'DAF',
		5 => 'DDP',
		6 => 'DDU',
		7 => 'DAT',
		8 => 'EXW',
		9 => 'FAS',
		10 => 'FCA',
		11 => 'FOB',
		12 => 'CIP',
		14 => 'DAP',
	];
	// CURRENCIES
	const CURRENCIES = [
		1 => '€ - EUR',
		2 => '$ - USD'
	];
	// COSTS
	const COSTS = [
		1 => 'invoice',
		2 => 'fob',
		3 => 'oceanfreight',
		4 => 'baf',
		5 => 'caf',
		6 => 'thc',
		7 => 'insurance',
		8 => 'extracharges',
	];
	
	public function translateCosts($key) {
		$costTranslations = [
			1 => Lx::t('frontend', 'Invoice Value'),
			2 => Lx::t('frontend', 'FOB'),
			3 => Lx::t('frontend', 'Ocean Freight'),
			4 => Lx::t('frontend', 'BAF'),
			5 => Lx::t('frontend', 'CAF'),
			6 => Lx::t('frontend', 'THC'),
			7 => Lx::t('frontend', 'Insurance'),
			8 => Lx::t('frontend', 'Extra Charges'),
		];
		
		return !empty($costTranslations[$key]) ? $costTranslations[$key] : '';
	}
	
	const PAGE_SIZE = 10;
	
	// date filter options
	const DATE_WEEK_ONE = '1 WEEK';
	const DATE_MONTH_ONE = '1 MONTH';
	const DATE_MONTH_THREE = '3 MONTH';
	const DATE_ALL = 'all';
	
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
	public static function provider($perPage = self::PAGE_SIZE, $search = null, $date = self::DATE_ALL, $exceptionIds = [], $withOfficialDoc = false, $city = null, $status = null, $reject = null, $page = null) {
		$query = self::find();
  
		
		$query->joinWith(['client as u1', 'maker as u2', 'invoicer as u3']);
		
		if (!empty($search)) {
			$query->leftjoin('user ut', 'ut.id = u1.id_user');
			$query->andFilterWhere(['like', 'ut.name', $search]);
			
			$query->orFilterWhere(['like', 'requester_name', $search])->orFilterWhere([
				'like',
				'requester_email',
				$search
			])->orFilterWhere(['like', 'vessel_bl_nr', $search])
				->orFilterWhere(['like', 'u1.name', $search])
				->orFilterWhere(['like', 'u1.name', $search])
				->orFilterWhere(['like', 'id_certificate', $search]);
		}
		
		if(is_numeric($city))
			$query->where(['certificate.city' => $city]);

		if(is_numeric($status))
			$query->where(['certificate.last_status' => $status]);			
		
		
		$user = Yii::$app->user->identity;
		if ($user->type == User::TYPE_CLIENT) {
			// verify if is top user from company
			if (empty($user->id_user)) {
				// g\et all users IDs from company (top user) and merge with user IDs array
				$usersIds = array_column(User::find()
					->select('id')
					->where(['id_user' => $user->id])
					->asArray()
					->all(), 'id');
			}
			$usersIds[] = $user->id;
			
			$query->andWhere(['in', 'id_user_client', $usersIds]);
		} else if ($user->type == User::TYPE_MAKER) {
			$currentStatus = CertificateStatus::STATUS_SUBMITTED.",".CertificateStatus::STATUS_DRAFT;
			$query->andWhere("id_user_maker = {$user->id} OR (id_user_maker IS NULL AND last_status IN ({$currentStatus}))");
		} else if ($user->type == User::TYPE_INVOICER) {
			$currentStatus = CertificateStatus::STATUS_ACCEPTED;
			$query->andWhere("id_user_invoicer = {$user->id} OR (id_user_invoicer IS NULL AND last_status = {$currentStatus})");
		}
		$query->andWhere('id_user_client IS NOT NULL');
		
		if (!empty($exceptionIds)) {
			$query->andWhere(['not in', 'id_certificate', $exceptionIds]);
		}
		
		if ($date != 'all' and !empty($date)) {
			$query->andWhere("modified >= DATE_SUB(NOW(), INTERVAL $date)");
		}
		
		if ($withOfficialDoc) {
			$query->andWhere("id_docs_certificate IS NOT NULL");
		}

		if(!is_null($reject)){
			$query->andWhere("reject = 1");
		}
		
		$query->orderBy('created DESC');
		
		$dataProviderParams = ['query' => $query, 'pagination' => ['pageSize' => $perPage, 'page' => ($page-1)]];
		$dataProvider = new ActiveDataProvider($dataProviderParams);
		
		return $dataProvider;
	}
	
	// todo: check permissions
	static public function checkChangeStatusPermissions() {
		return true;
	}
	
	/**
	 * @param $id
	 * @param null $description
	 *
	 * @param bool $validate
	 * @param bool $validated
	 * @param null $price
	 * @param null $status
	 *
	 * @return void
	 */
	static public function changeStatus($id, $description = null, $validate = false, $validated = false, $price = null, $status = null) {
		if (!self::checkChangeStatusPermissions()) {
			exit;
		}
		
		$certificate = self::findOne($id);
		
		$statusOptions = CertificateStatus::getStatusOptions();
		
		if ($certificate) {
			//unset reject
			$certificate->reject = 0;
            
            // create new certificate status
            $certificateStatus = new CertificateStatus();
            $certificateStatus->id_certificate = $certificate->primaryKey;
            
            $user = Yii::$app->user->identity;
            
            $idUserMaker = ($certificate->last_status == CertificateStatus::STATUS_SUBMITTED && $certificate->id_user_maker == null)? $user->id : null;
            
            if (!$validate || ($validate && $validated)) {
                $newStatus = !is_null($status)? (int)$status : null;
                
                if (is_null($newStatus)) {
                    $isRequest = in_array($certificate->last_status,
                        [CertificateStatus::STATUS_CREATED, CertificateStatus::STATUS_REJECTED]);
                    $isSubmitted = in_array($certificate->last_status, [CertificateStatus::STATUS_SUBMITTED]);
                    
                    if ($isRequest) {
                        $newStatus = CertificateStatus::STATUS_SUBMITTED;
                    }
                    
                    if ($isSubmitted) {
                        $newStatus = CertificateStatus::STATUS_PROCESS;
                    }
                    
                    //Automatic Roll
                    if (!($isRequest || $isSubmitted)) {
                        $isCreated = $certificate->last_status == CertificateStatus::STATUS_CREATED;
                        $hasInconsistencyValidated = $validated && $certificate->last_status != CertificateStatus::STATUS_VALIDATION;
                        $isRejected = $certificate->last_status == CertificateStatus::STATUS_REJECTED;
                        $isProcess = !$hasInconsistencyValidated && $certificate->last_status == CertificateStatus::STATUS_PROCESS;
                        $isDraftValidation = $certificate->last_status == CertificateStatus::STATUS_DRAFT_VALIDATION;
                        $isToValidate = $certificate->last_status == CertificateStatus::STATUS_TO_VALIDATE;
                        $isInvoice = $certificate->last_status == CertificateStatus::STATUS_ACCEPTED;//ex invoice
                        $isEmitted = $certificate->last_status == CertificateStatus::STATUS_EMITTED;
                        $isInvoiced = $certificate->last_status == CertificateStatus::STATUS_INVOICE;
                        $isPaymentValidation = $certificate->last_status == CertificateStatus::STATUS_PAYMENT_VALIDATION;
                        $hasCustomFields = $isProcess || $isDraftValidation || $isToValidate || $isInvoice || $isPaymentValidation || $isEmitted || $isInvoiced;
                        
                        $isAdvance = ($isCreated || $hasInconsistencyValidated) && !$hasCustomFields;
    
                        if ($isInvoiced) {
                            $newStatus = CertificateStatus::STATUS_PAYMENT_VALIDATION;
                        }
    
                        if ($isPaymentValidation) {
                            $newStatus = CertificateStatus::STATUS_TO_VALIDATE;
                        }
                        
                        if ($isInvoice) {
                            $newStatus = CertificateStatus::STATUS_INVOICE;
                        }
                        
                        if ($isToValidate) {
                            $newStatus = CertificateStatus::STATUS_EMITTED;
                        }
                        
                        // step 4 to 6
                        if ($isDraftValidation) {
                            $newStatus = CertificateStatus::STATUS_ACCEPTED;
                        }
                        
                        if ($isProcess) {
                            $newStatus = CertificateStatus::STATUS_DRAFT_VALIDATION;
                        }
                        
                        if ($isEmitted) {
                            $newStatus = CertificateStatus::STATUS_CERTIFICATE_CLOSURE;
                        }
                        
                        if ($isAdvance) {
                            $newStatus = $certificate->last_status + 2;
                        }
                        
                        if (!$isAdvance && $isRejected && !$hasCustomFields) {
                            $newStatus = $certificate->id_user_invoicer == null? CertificateStatus::STATUS_PROCESS : die('HERE');
                        }
                        
                        if (!($isAdvance && $isRejected) && !$hasCustomFields) {// 6 to 7
                            $newStatus = $certificate->last_status + 1;
                        }
                    }
                    
                    /**
                     * START NO ACTIONS
                     * Case 1 - Verify if hasn't assign maker in the draft submmited
                     *     DO: Assign to me
                     * */
                    $hasMaker = is_numeric($certificate->id_user_maker) && $certificate->id_user_maker > 0;
                    
                    // Case 1 - Verify if has assign maker in the draft submmited
                    if (!$hasMaker && $certificate->last_status == CertificateStatus::STATUS_SUBMITTED) {
                        $newStatus = $certificate->last_status;
                        //Assign to me
                        $certificate->id_user_maker = $user->getId();
                    }
                    /**
                     * END NO ACTIONS
                     */
                }
            } else {
                if ($validate && !$validated) {
                    
                    //Rejects Rules
					$newStatus = CertificateStatus::STATUS_REJECTED;
					
					//set rejected
					$certificate->reject = 1;
                    
                    $lastStatusRulesToDraft = in_array($certificate->last_status, [CertificateStatus::STATUS_PROCESS]);
                    $lastStatusRulesToCreated = in_array($certificate->last_status,
                        [CertificateStatus::STATUS_SUBMITTED]);
                    $lastStatusRulesToDraftValidation = in_array($certificate->last_status,
                        [CertificateStatus::STATUS_DRAFT_VALIDATION]);
                    $lastStatusRulesToPaymentValidation = in_array($certificate->last_status,
                        [CertificateStatus::STATUS_PAYMENT_VALIDATION]);
                    
                    if ($lastStatusRulesToDraft) {
                        $newStatus = CertificateStatus::STATUS_DRAFT;
                    }
                    
                    if ($lastStatusRulesToCreated) {
                        $newStatus = CertificateStatus::STATUS_CREATED;
                    }
                    
                    if ($lastStatusRulesToDraftValidation) {
                        $newStatus = CertificateStatus::STATUS_PROCESS;
                    }
                    
                    if ($lastStatusRulesToPaymentValidation) {
                        $newStatus = CertificateStatus::STATUS_INVOICE;
                    }
                }
            }
            
			$quantityOptions = is_array($statusOptions)? max(array_keys($statusOptions)) : 0;
			$newStatus = $newStatus > $quantityOptions? $certificate->last_status : $newStatus;
			$certificateStatus->status = $newStatus;
			$certificateStatus->obs = $price != null? (string)$price : $description;
			$certificateStatus->id_user = $user->id;
			
			if(is_null($certificateStatus->created))
				$certificateStatus->created = new Expression('NOW()');
    
			
			// update certificate last status
			if ($certificateStatus->save()) {
				$certificate->last_status = $certificateStatus->status;
    
				// associate user ID
				if ($certificateStatus->status == CertificateStatus::STATUS_PROCESS && $certificate->id_user_maker == null) {
					$certificate->id_user_maker = $user->id;
				} else if ($certificateStatus->status == CertificateStatus::STATUS_INVOICE && $certificate->id_user_invoicer == null) {
					$certificate->id_user_invoicer = $user->id;
				} else if (!is_null($idUserMaker)) {
					$certificate->id_user_maker = $idUserMaker;
				}
				
				// add price
				if ($price != null && empty($certificate->price)) {
					$certificate->price = (float)$price;
				}
    
				$certificate->save(false);
				
				// send email
				$certificate->sendEmail();
			}
		}else{
			die('out if changeStatus()');
		}
	}
	
	/*
	 * generate Invoice for one - or many - certificates passed by POST,
	 * this turned out to be a rushed job... so it's missing proper Form declaration, load, validation and save :(
	 */
	public static function generateInvoice(){
		$post = Yii::$app->request->post();
        
		$certificateModels = [];
		$idsCertificates = $post['id-certificates'];
		$idUserClient = $post['id-user-client']; // this is the company
		$nr = $post['invoice-nr'];
		$price = $post['invoice-price'];
		$date = $post['invoice-date'];
		$certificates = explode(",",$idsCertificates);
		$file = UploadedFile::getInstanceByName('invoice-file-ajax');
		$total = 0;
		$description = ''; // for now it's empty
		
		// get total certificate
		foreach($certificates as $idCertificate){
			$certificateModel = Certificate::find()->where(['id_certificate'=>$idCertificate])->one();
			$total+= $certificateModel->price;
			array_push($certificateModels,$certificateModel);
		}
		
		// create invoice entry
		$invoiceModel = new Invoices();
		$invoiceModel->id_user_client = $idUserClient;
		$invoiceModel->id_user_invoicer = Yii::$app->user->id;
		$invoiceModel->total = $total;
		$invoiceModel->status = 1;
		$invoiceModel->obs= $description;
		$invoiceModel->price= $price;
		$invoiceModel->date= $date;
		$invoiceModel->nr= $nr;
		$invoiceModel->save();
		
		// upload file to invoice
		$docInvoice = new Doc();
		$fileName = 'docs/invoice_' . $invoiceModel->primaryKey . '_' . $file->baseName . '_' . time() . '.' . $file->extension;
		$fileName = preg_replace('/\s+/', '', $fileName);
		$docInvoice->path = $fileName;
		$docInvoice->name = $file->baseName;
		$docInvoice->save();
		$file->saveAs($fileName);
		$invoiceModel->id_doc = $docInvoice->id_doc;
		$invoiceModel->save();
		
		// associating all certificates to invoice entry && change Status
		foreach($certificateModels as $certificateModel){
			$invoiceModelCertificate = new InvoicesCertificates();
			$invoiceModelCertificate->id_invoice = $invoiceModel->primaryKey;
			$invoiceModelCertificate->id_certificate = $certificateModel->primaryKey;
			$invoiceModelCertificate->save();
			
			Certificate::changeStatus($certificateModel->primaryKey, $description, 0, 0);
		}
	}
	
	/*
	 * generate Draft Validation for one certificate passed by POST.
	 */
	public static function generateDraftSiga(){
		$post = Yii::$app->request->post();
		
		$id = $post['idCertificate'];
		$file = UploadedFile::getInstanceByName('siga-file-ajax');
		
		// upload file to invoice
		$docDraft = new Doc();
		$fileName = 'docs/siga_draft_request_' . $id . '_' . $file->baseName . '_' . time() . '.' . $file->extension;
		$fileName = preg_replace('/\s+/', '', $fileName);
		$docDraft->path = $fileName;
		$docDraft->name = $file->baseName;
		$docDraft->save();
		$file->saveAs($fileName);
		
		$certificate = Certificate::find()->where(['id_certificate'=>$id])->one();
		
		$certificate->id_docs_siga_draft_request = $docDraft->id_doc;
		$certificate->save();
		
		Certificate::changeStatus($certificate->primaryKey, 'ARC CREATION', 0, 0);
	}
	
	/*
	 * generate Reject Files for one certificate passed by POST.
	 */
	public static function generateDraftValidation(){
		$post = Yii::$app->request->post();
		
		$id = $post['idCertificate'];
		$files = UploadedFile::getInstancesByName('draft-validation-file-ajax');
		
		$docList = '';
		foreach($files as $key=>$file){
			$doc[$key] = new Doc();
			$fileName = 'docs/rejecteds_files_' . $id . '_' . $file->baseName . '_' . time() . '.' . $file->extension;
			$fileName = preg_replace('/\s+/', '', $fileName);
			$doc[$key]->path = $fileName;
			$doc[$key]->name = $file->baseName;
			$doc[$key]->save();
			$file->saveAs($fileName);
			
			$docList .= $doc[$key]->id_doc.';';
		}
		$certificate = Certificate::find()->where(['id_certificate'=>$id])->one();
		
		$certificate->id_docs_rejected_draft_validation = $docList;
		$certificate->save();
		
		Certificate::changeStatus($certificate->primaryKey, 'REJECT DRAFT VALIDATION', 1, 0);
	}
	
	/*
	 * generate Validated File for one certificate passed by POST.
	 */
	public static function generateValidatedFile(){
		$post = Yii::$app->request->post();
		
		$id = $post['idCertificate'];
        $id = $post['idCertificate'];
        $nr = $post['draft-nr'];
        $value = $post['draft-value'];
		$file = UploadedFile::getInstanceByName('validated-file-ajax');
		
		$doc = new Doc();
		$fileName = 'docs/validated_file_' . $id . '_' . $file->baseName . '_' . time() . '.' . $file->extension;
		$fileName = preg_replace('/\s+/', '', $fileName);
		$doc->path = $fileName;
		$doc->name = $file->baseName;
		$doc->save();
		$file->saveAs($fileName);
		
		$certificate = Certificate::find()->where(['id_certificate'=>$id])->one();
		
		$certificate->id_docs_siga_draft_request = $doc->id_doc;
        $certificate->arc_number = $nr;
        $certificate->arc_value = $value;
		$certificate->save();
		
		Certificate::changeStatus($certificate->primaryKey, 'VALIDATED FILE', 0, 0);
	}
    
    /*
     * generate a Prove Payment File for one certificate passed by POST.
     */
    public static function generateProvePayment(){
		$post = Yii::$app->request->post();
        
        $id = $post['idCertificate'];
		$file = UploadedFile::getInstanceByName('prove-file-ajax');
		
		$hasOtherPaymentMethod = isset($post['proof-value']) && !empty(isset($post['proof-value']));
		$hasFile = !is_null($file);
		
		if($hasFile || $hasOtherPaymentMethod){

			$certificate = Certificate::find()->where(['id_certificate'=>$id])->one();
        
			if($hasFile){
				$doc = new Doc();
				$fileName = 'docs/validated_file_' . $id . '_' . $file->baseName . '_' . time() . '.' . $file->extension;
				$fileName = preg_replace('/\s+/', '', $fileName);
				$doc->path = $fileName;
				$doc->name = $file->baseName;
				$doc->save();
				$file->saveAs($fileName);
				
				$certificate->id_docs_proof_of_payment = $doc->id_doc;
			}

			if($hasOtherPaymentMethod){
				$certificate->payment_method = is_numeric($post['payment-method'])? $post['payment-method']:null;
				$certificate->other_payment_proof = $post['proof-value'];
			}
		
		
			$certificate->save();		
			Certificate::changeStatus($certificate->primaryKey, 'PROOF OF PAYMENT', 0, 0);
		}
    }

	
	public static function uploadCertificate($id){
		// upload file to invoice
		$file = UploadedFile::getInstanceByName('invoice-file-ajax');
		$docInvoice = new Doc();
		$fileName = 'docs/certificate_' . $id . '_' . $file->baseName . '_' . time() . '.' . $file->extension;
		$fileName = preg_replace('/\s+/', '', $fileName);
		$docInvoice->path = $fileName;
		$docInvoice->name = $file->baseName;
		$docInvoice->save();
		
		$certificate = Certificate::find()->where(['id_certificate'=>$id])->one();
		$certificate->id_docs_certificate = $id;
		$certificate->save();
		
		return $file->saveAs($fileName);
	}
	
	/**
	 * @return array|\common\models\Certificate[]|\yii\db\ActiveRecord[]
	 */
	public function getDocuments() {
		return $this->find()
			->where(['id_certificate' => $this->primaryKey])
			->select('docs_bill_lading, docs_commercial_invoice, docs_freight_invoice, docs_draft_request_signed')
			->asArray()
			->one();
	}
	
	/**
	 * @return array
	 */
	public static function getDateOptions() {
		return [
			self::DATE_WEEK_ONE => 'Last Week',
			self::DATE_MONTH_ONE => 'Last Month',
			self::DATE_MONTH_THREE => 'Last Three Months',
			self::DATE_ALL => 'All',
		];
	}
	
	public function sendEmail() {
	    //Send Email Rules
		switch ($this->last_status) {
			case CertificateStatus::STATUS_REJECTED:
			case CertificateStatus::STATUS_ACCEPTED:
			case CertificateStatus::STATUS_INVOICE:
			case CertificateStatus::STATUS_EMITTED:
				$idUserTo = $this->id_user_client;
				break;
			default:
				$idUserTo = $this->id_user_client;
		}
		
		$user = !empty($idUserTo) ? User::findOne(['id' => $idUserTo]) : null;
		
		if (!$user) {
			return false;
		}
		
		//SEND EMAIl WITH ATTACH
		switch ($this->last_status) {
			case CertificateStatus::STATUS_CREATED:
                //var_dump('post Created');die();
				//$subject = Yii::t('email/certificate', 'Certificate Created | Angdocs');
				break;
			case CertificateStatus::STATUS_REJECTED:
                //var_dump('post Rejected');die();
				//$subject = Yii::t('email/certificate', 'Certificate Created | Angdocs');
				break;
			case CertificateStatus::STATUS_ACCEPTED:
                //var_dump('post Accepted');die();
				//$subject = Yii::t('email/certificate', 'Certificate Created | Angdocs');
				break;
			case CertificateStatus::STATUS_INVOICE:
                //var_dump('post Invoice');die();
				//$filePath = $this->invoicesCertificates->invoice->doc->path;
				//$subject = Yii::t('email/certificate', 'Certificate Created | Angdocs');
				break;
			case CertificateStatus::STATUS_EMITTED:
                //var_dump('post Emitted');die();
			    //$subject = Yii::t('email/certificate', 'Certificate Emitted | Angdocs');
				break;
			default:
                //var_dump('nulo');die();
				//$subject = null;
		}
		/*
            CertificateStatus::STATUS_CREATED//1
            CertificateStatus::STATUS_SUBMITTED//2
            CertificateStatus::STATUS_PROCESS//3
            CertificateStatus::STATUS_DRAFT_VALIDATION//4
            CertificateStatus::STATUS_ACCEPTED//6
            CertificateStatus::STATUS_INVOICE//7
            CertificateStatus::STATUS_PAYMENT_VALIDATION//8
            CertificateStatus::STATUS_TO_VALIDATE//5
            CertificateStatus::STATUS_EMITTED//9
            CertificateStatus::STATUS_CERTIFICATE_CLOSURE//10
		*/
		
		//Force insert manually
        $subject = null;
		if ($subject == null) {
			return false;
		}
		
		// send email to user
		$email = Yii::$app->mailer->compose([
			'html' => "certificate-status-html",
			'text' => "certificate-status-text"
		], ['certificate' => $this])
			->setFrom([Yii::$app->params['smtpEmail'] => 'Angdocs'])
			// todo: uncomment
			//            ->setTo([$user->email, Yii::$app->params['adminEmail']])
			->setTo(Yii::$app->params['adminEmail'])
			->setSubject($subject);
		
		// attach file
		if (!empty($filePath)) {
			$email->attach($filePath);
		}
		
		@$email->send();
		
		return true;
	}
	
	public static function findOne($id){
		// this is almost copy/past from provider function
		$query = self::find();
		$query->where(['id_certificate' => $id]);
		$query->joinWith(['client as u1', 'maker as u2', 'invoicer as u3']);
		
		$user = Yii::$app->user->identity;
		if ($user->type == User::TYPE_CLIENT) {
			// verify if is top user from company
			$usersIds[] = $user->id;
			if (empty($user->id_user)) {
				// get all users IDs from company (top user) and merge with user IDs array
				$usersIds += array_column(User::find()
					->select('id')
					->where(['id_user' => $user->id])
					->asArray()
					->all(), 'id');
			}
			$query->andWhere(['in', 'id_user_client', $usersIds]);
		} else if ($user->type == User::TYPE_MAKER) {
			$currentStatus = CertificateStatus::STATUS_SUBMITTED.",".CertificateStatus::STATUS_DRAFT;
			$query->andWhere("id_user_maker = {$user->id} OR (id_user_maker IS NULL AND last_status IN ({$currentStatus}))");
		} else if ($user->type == User::TYPE_INVOICER) {
			$currentStatus = CertificateStatus::STATUS_ACCEPTED;
			$query->andWhere("id_user_invoicer = {$user->id} OR (id_user_invoicer IS NULL AND last_status = {$currentStatus})");
		}
		$query->andWhere('id_user_client IS NOT NULL');
		
		
		return $query->one();
	}
}
