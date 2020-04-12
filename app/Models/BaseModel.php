<?php


namespace App\Models;


use Encore\Admin\Facades\Admin;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected $connection = 'mysql';
    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'update_at';
    protected $guarded = [];
    protected static $status = [
        '0' => '禁用',
        '1' => '启用'
    ];

    /**
     * 查询状态作用域
     * @param $query
     * @param null $status
     * @return mixed
     */
    public function scopeStatus($query, $status = null)
    {
        return is_null($status) ? $query : $query->where('status', $status);
    }

    /**
     * 翻译字段--Gird显示变量
     * @param $field
     * @param $value
     * @param string $class
     * @return mixed
     */
    public function _girdVar($field, $value = null, $class='')
    {
        $class=$class ? $class : $this->getSelf();
        return call_user_func_array([$class, '_gird_'.$field], [$value]);
    }

    /**
     * 翻译字段--获得静态类
     * @return string
     */
    public function getSelf()
    {
        return __CLASS__;
    }

    /**
     * 翻译字段--数据状态
     * @param $value
     * @return mixed
     */
    public static function _gird_status($value)
    {
        return self::$status[$value] ?: '';
    }

    /**
     * 获取字段--数据状态
     * @return array
     */
    public static function _gird_status_all()
    {
        return self::$status;
    }


    public function getAdminIdAttribute($value)
    {
        return AdminUsers::find($value)->name;
    }

    public function setAdminIdAttribute($value)
    {

        $this->attributes['admin_id'] = Admin::user()->id;
    }
}