<?php


namespace App\Libs\Site\ZenCart\Models;


class RecordCompanyInfo extends BaseModel
{
    public $table = 'record_company_info';
    protected $_pk = ['record_company_id','languages_id'];
    public function company()
    {
        return $this->belongsTo(RecordCompany::class,'record_company_id');
    }

}