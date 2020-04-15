<?php

namespace App\Admin\Actions\Site;

use App\Admin\Extensions\Actions\XFormBatchAction;
use App\Libs\Site\ZenCart\Models\Admin;
use App\Libs\Site\ZenCart\ZenCart;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class ResetPass extends XFormBatchAction
{
    public $name = '重置后台密码';
    protected $selector = '.reset-pass';

    public function html()
    {
        return <<<HTML
        <a class="btn btn-sm btn-default reset-pass">重置后台密码</a>
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
        $pass = $request->get('password');
        $new_pass = ZenCart::password_hash($pass);
        foreach ($collection as $model) {
            $config = $model->config;
            $server = $model->server;
            $admin = (new Admin($server->ip,$config->db_user,$config->db_pass,$config->db_name))->find($admin_id);
            if (!$admin){
                $errors[] = sprintf("[#%s]%s:ID为%s的用户不存在",$model->id,$model->domain,$admin_id);
                continue;
            }
            $admin->update(['admin_pass'=>$new_pass,'pwd_last_change_date'=>now()]);
            $n++;
        }
        if ($n) {
            return $this->response()->success(action_msg($this->name,$n,$errors))->refresh();
        }
        return $this->response()->error(action_msg($this->name,$n,$errors));
    }
}