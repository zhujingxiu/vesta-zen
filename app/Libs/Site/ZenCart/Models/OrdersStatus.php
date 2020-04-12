<?php


namespace App\Libs\Site\ZenCart\Models;


class OrdersStatus extends BaseModel
{
    public $table = 'orders_status';

    public function orders()
    {
        return $this->hasMany(Orders::class,'orders_status' );
    }
}