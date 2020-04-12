<?php


namespace App\Models;


class Domain extends BaseModel
{
    protected $table = 'hz_domains';

    public function cloudflare()
    {
        return $this->belongsTo(CloudFlare::class,'cf_id');
    }


}