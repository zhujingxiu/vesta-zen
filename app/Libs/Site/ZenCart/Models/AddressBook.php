<?php


namespace App\Libs\Site\ZenCart\Models;


class AddressBook extends BaseModel
{
    public $table = 'address_book';

    public function customer()
    {
        return $this->belongsTo(Customers::class,'customers_id');
    }

}