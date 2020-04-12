<?php


namespace App\Libs\Site\ZenCart\Models;


class Reviews extends BaseModel
{
    public $table = 'reviews';
    public $timestamps = true;

    public function product()
    {
        return $this->belongsTo(Products::class,'products_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customers::class,'customers_id');
    }

    public function descriptions()
    {
        return $this->hasMany(ReviewsDescription::class,'reviews_id');
    }
}