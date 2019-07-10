<?php

namespace common\models;

use Yii;
use \common\models\base\InvoicesCertificates as BaseInvoicesCertificates;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "invoices_certificates".
 */
class InvoicesCertificates extends BaseInvoicesCertificates
{
    public function rules()
    {
        return ArrayHelper::merge(
             parent::rules(),
             [
                  # custom validation rules
             ]
        );
    }
}
