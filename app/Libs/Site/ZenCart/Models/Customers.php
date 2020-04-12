<?php


namespace App\Libs\Site\ZenCart\Models;


class Customers extends BaseModel
{
    public $table = 'customers';

    public function orders()
    {
        return $this->hasOne(Orders::class,'customers_id');
    }

    public function addresses()
    {
        return $this->hasMany(AddressBook::class,$this->primaryKey);
    }

    public function groups()
    {
        return $this->belongsTo(GroupPricing::class,'customers_group_pricing');
    }

    public function reviews()
    {
        return $this->hasMany(Reviews::class,'customers_id');
    }
}