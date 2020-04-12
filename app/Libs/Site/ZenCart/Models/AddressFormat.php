<?php


namespace App\Libs\Site\ZenCart\Models;


class AddressFormat extends BaseModel
{
    public $table = 'address_format';

    public function customerOrders()
    {
        return $this->hasMany(Orders::class,'customers_address_format_id');
    }

    public function deliveryOrders()
    {
        return $this->hasMany(Orders::class,'delivery_address_format_id');
    }
}