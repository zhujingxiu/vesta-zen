<?php


namespace App\Models;


class ServerGroup extends BaseModel
{
    protected $table = "hz_server_groups";
    public function admin()
    {
        return $this->belongsTo(AdminUsers::class);
    }
}