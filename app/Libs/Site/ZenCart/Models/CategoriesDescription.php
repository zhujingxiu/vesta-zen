<?php


namespace App\Libs\Site\ZenCart\Models;


class CategoriesDescription extends BaseModel
{
    protected $table = 'categories_description';

    protected $_pk = ['categories_id','language_id'];

    public function categories()
    {
        return $this->belongsTo(Categories::class,'categories_id');
    }
}