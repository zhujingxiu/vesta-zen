<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'hz_setting';
    public $timestamps = false;
    protected $admin_user = false;
}