<?php

namespace App\Models;


class Server extends BaseModel
{
    protected $table = "hz_servers";


    protected static $status = [
        1 => '正常',
        -1 => '暂停',
        0 => '关闭',
    ];
    public static function _gird_status_all()
    {
        return self::$status;
    }

    public function group()
    {
        return $this->belongsTo(ServerGroup::class);
    }

    public function admin()
    {
        return $this->belongsTo(AdminUsers::class,'admin_id');
    }
}
