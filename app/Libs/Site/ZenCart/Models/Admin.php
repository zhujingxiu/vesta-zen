<?php


namespace App\Libs\Site\ZenCart\Models;


class Admin extends BaseModel
{
    public $table = 'admin';
    protected $_pk = 'admin_id';

    public function activity()
    {
        return $this->hasMany(AdminActivityLog::class,'admin_id');
    }

    public function profile()
    {
        return $this->belongsTo(AdminProfile::class,'admin_profile');
    }
}