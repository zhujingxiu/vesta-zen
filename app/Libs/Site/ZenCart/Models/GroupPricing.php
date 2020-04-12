<?php


namespace App\Libs\Site\ZenCart\Models;


class GroupPricing extends BaseModel
{
    public $table = 'group_pricing';
    public $timestamps = true;
    protected $_pk = 'group_id';
    public function customers()
    {
        return $this->hasMany(Customers::class,'customers_group_pricing');
    }
}