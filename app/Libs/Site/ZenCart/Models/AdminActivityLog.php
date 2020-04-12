<?php


namespace App\Libs\Site\ZenCart\Models;


class AdminActivityLog extends BaseModel
{
    public $table = 'admin_activity_log';
    protected $_pk = 'log_id';

    public function admin()
    {
        return $this->belongsTo(Admin::class,'admin_id');
    }
}