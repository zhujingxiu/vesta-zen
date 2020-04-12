<?php


namespace App\Libs\Site\ZenCart\Models;


class OrdersTotal extends BaseModel
{
    public $table = 'orders_total';
    public $timestamps = true;
    public const CREATED_AT = 'date_purchased';

    public function order()
    {
        return $this->belongsTo(Orders::class,'orders_id');
    }

}