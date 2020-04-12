<?php

namespace App\Admin\Actions\Site;

use App\Libs\Site\ZenCart\Models\Admin;
use App\Libs\Site\ZenCart\ZenCart;
use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class ResetPwd extends BatchAction
{
    public $name = '重置后台密码';
    protected $selector = '.reset-pwd';

    public function html()
    {
        return <<<HTML
        <a class="btn btn-sm btn-default reset-pwd">重置后台密码</a>
HTML;
    }

    public function form()
    {
        $min_length = config('site.site_password_min');
        $this->text('admin_id', '用户ID')->default(1)->help('默认是ID=1的admin账户，或者指定其他ID');
        $this->password('password', '密码')
            ->rules(sprintf('required|min:%s|confirmed|regex:/^(?=.*[a-zA-Z]+.*)(?=.*[\d]+.*)[\d\w\s[:punct:]]{%s,}$/',
                $min_length, $min_length))->help(sprintf(config('site.site_password_alert'), $min_length));
        $this->password('password_confirmation', '确认密码');

    }

    public function handle(Collection $collection, Request $request)
    {
        $n=0;
        $errors = [];
        $admin_id = $request->get('admin_id');
        if (!is_numeric($admin_id) || $admin_id<1){
            return $this->response()->error('重置后台密码失败：请输入有效用户ID');
        }
        $pwd = $request->get('password');
        $new_pwd = ZenCart::password_hash($pwd);
        foreach ($collection as $model) {
            $config = $model->config;
            $server = $model->server;
            $conn = new_db_connection($server->ip,$config->db_user,$config->db_pwd,$config->db_name);
            $admin = (new Admin())->setConnection($conn)->find($admin_id);
            if (!$admin){
                $errors[] = sprintf("[#%s]%s ID为%s的用户不存在",$model->id,$model->domain,$admin_id);
                continue;
            }
            $admin->update(['admin_pass'=>$new_pwd,'pwd_last_change_date'=>now()]);
            $n++;
        }
        if ($n) {
            return $this->response()->success(sprintf('重置后台密码：%s个站点成功，错误信息：%s', $n, implode("<br>", $errors)))->refresh();
        }
        return $this->response()->error(sprintf('重置后台密码失败：%s', implode('<br>', $errors)));
    }
}