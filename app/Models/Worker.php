<?php


namespace App\Models;


class Worker extends BaseModel
{
    protected $table = 'hz_workers';
    public $timestamps = false;

    public function admin()
    {
        return $this->belongsTo(AdminUsers::class);
    }

    protected static $gender = [
        '0' => '保密',
        '1' => '男',
        '2' => '女',
    ];

    public static function _gird_gender($value)
    {
        return self::$gender[$value] ?: '';
    }

    /**
     * 获取字段--数据状态
     * @return array
     */
    public static function _gird_gender_all()
    {
        return self::$gender;
    }
}