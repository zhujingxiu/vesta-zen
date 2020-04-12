<?php


namespace App\Libs\Site\ZenCart\Models;


class Featured extends BaseModel
{
    public $table = 'featured';
    public $timestamps = true;
    public const CREATED_AT = 'featured_date_added';
    public const UPDATED_AT = 'featured_last_modified';

    public function product()
    {

    }
}