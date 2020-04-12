<?php


namespace App\Libs\Site\ZenCart\Models;


class AdminProfile extends BaseModel
{
    public $table = 'admin_profiles';
    protected $_pk = 'profile_id';

    public function admins()
    {
        return $this->hasMany(Admin::class,'admin_profile');
    }
}