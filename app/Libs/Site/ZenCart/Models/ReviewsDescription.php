<?php


namespace App\Libs\Site\ZenCart\Models;


class ReviewsDescription extends BaseModel
{
    public $table = 'reviews_description';
    public $incrementing = false;
    protected $_pk = ['reviews_id','languages_id'];

    public function review()
    {
        return $this->belongsTo(Reviews::class,'reviews_id');
    }

}