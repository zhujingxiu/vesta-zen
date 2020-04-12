<?php


namespace App\Libs\Site\ZenCart\Models;


class Specials extends BaseModel
{
    public $table = 'specials';

    public $timestamps = true;

    public const CREATED_AT = 'specials_date_added';
    public const UPDATED_AT = 'specials_last_modified';

    public function product()
    {
        return $this->belongsTo(Products::class,'products_id');
    }
}