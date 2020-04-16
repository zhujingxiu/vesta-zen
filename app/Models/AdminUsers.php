<?php

namespace App\Models;


use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AdminUsers extends Administrator
{
    protected $guarded = [];


    public function worker():HasOne
    {
        return $this->hasOne(Worker::class,'admin_id');
    }
}
