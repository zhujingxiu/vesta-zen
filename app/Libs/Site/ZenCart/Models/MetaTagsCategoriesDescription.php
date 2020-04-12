<?php


namespace App\Libs\Site\ZenCart\Models;


class MetaTagsCategoriesDescription extends BaseModel
{
    public $table = 'meta_tags_categories_description';
    protected $_pk = ['categories_id','language_id'];

    public function category()
    {
        return $this->belongsTo(Categories::class,'categories_id');
    }
}