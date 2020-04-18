<?php

namespace App\Admin\Actions\Site;


use App\Admin\Extensions\Actions\XFormBatchAction;
use App\Libs\Site\Site;
use Carbon\Carbon;
use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Logo extends XFormBatchAction
{
    public $name = '设置网站LOGO';
    protected $selector = '.site-logo';

    public function html()
    {
        return <<<HTML
        <a class="btn btn-sm btn-default site-logo">设置网站LOGO</a>
HTML;
    }

    public function form()
    {
        $this->image('logo', 'Logo')
            ->help('1.上传最大8M的图片格式文件（gif）<br>2.将替换站点后台目录的images/logo.gif');
    }

    /**
     * @param UploadedFile $file
     * @return array
     */
    protected function validateImage(UploadedFile $file)
    {
        if (empty($file) || !$file->isValid()) {
            return msg_error('图片文件无效，请重新选择文件');
        }
        $size = $file->getClientSize();
        if ($size > 8 * 1024 * 1024) {
            return msg_error('最大支持8M');
        }
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, ['gif'])) {
            return msg_error('当前支持文件格式为gif');
        }
        return msg_success('');
    }

    public function handle(Collection $collection, Request $request)
    {
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $ret = $this->validateImage($file);
            if ($ret['code'] != 200) {
                return $this->response()->error($ret['msg']);
            }

        } else {
            return $this->response()->error('请先上传文件');
        }
        $n = 0;
        $errors = [];
        foreach ($collection as $model) {
            $config = $model->config;
            $server = $model->server;
            $ret = $this->replaceLogo($server->ip,$server->user,$server->pass,
                $config->fs_catalog,$config->admin_dir,$file);
            if ($ret['code']!=200){
                $errors[] = sprintf('[#%s]%s：%s',$model->id,$model->domain,$ret['msg']);
                continue;
            }
            $n++;
        }
        if ($n) {
            return $this->response()->success(action_msg($this->name,$n,$errors))->refresh();
        }
        return $this->response()->error(action_msg($this->name,$n,$errors));
    }

    /**
     * @param $server_ip
     * @param $server_user
     * @param $server_pass
     * @param $site_folder
     * @param $admin_dir
     * @param $file
     * @return array
     */
    protected function replaceLogo($server_ip,$server_user,$server_pass,$site_folder,$admin_dir,UploadedFile $file)
    {
        // 替换后台文件
        $logo = $file->getRealPath();
        $remote_file = sprintf("%s/%s/images/logo.gif", trim($site_folder,'/'), trim($admin_dir,'/'));
        $ret = ssh_send_file($server_ip,$server_user,$server_pass,$logo,$remote_file,log_hash(__METHOD__));
        if ($ret['code']!=200){
            return $ret;
        }
        return msg_success($site_folder);
    }

}