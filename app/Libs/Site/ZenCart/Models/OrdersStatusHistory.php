<?php


namespace App\Libs\Site\ZenCart\Models;


class OrdersStatusHistory extends BaseModel
{
    public $table = 'orders_status_history';

    public function order()
    {
        return $this->belongsTo(Orders::class,'orders_id' );
    }

    public function status()
    {
        return $this->belongsTo(OrdersStatus::class,'orders_status_id' );
    }
}