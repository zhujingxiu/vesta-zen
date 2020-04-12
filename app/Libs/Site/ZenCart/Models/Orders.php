<?php


namespace App\Libs\Site\ZenCart\Models;


class Orders extends BaseModel
{
    public $table = 'orders';

    public function customer()
    {
        return $this->belongsTo(Customers::class,'customers_id');
    }

    public function customerAddress()
    {
        return $this->belongsTo(AddressFormat::class,'customers_address_format_id');
    }

    public function deliveryAddress()
    {
        return $this->belongsTo(AddressFormat::class,'delivery_address_format_id');
    }

    public function total()
    {
        return $this->hasOne(OrdersTotal::class,'orders_id');
    }

    public function status()
    {
        return $this->belongsTo(OrdersStatus::class,'orders_status');
    }

    public function products()
    {
        return $this->hasMany(OrdersProducts::class,'orders_id')->with('product');
    }
}