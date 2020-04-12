<?php


namespace App\Libs\Site\ZenCart\Models;


class TaxRates extends BaseModel
{
    public $table = 'tax_rates';
    public $timestamps = true;

    public function taxClass()
    {
        return $this->belongsTo(TaxClass::class,'tax_class_id');
    }

    public function zone()
    {
        return $this->belongsTo(Zones::class,'tax_zone_id');
    }
}