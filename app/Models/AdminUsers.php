<?php

namespace App\Models;


class AdminUsers extends BaseModel
{
    protected $table = 'admin_users';

    protected $admin_user = false;
    /**
     * 不可被批量赋值的属性。
     *
     * @var array
     */
    protected $guarded = [];
}
