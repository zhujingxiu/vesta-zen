<?php

namespace App\Admin\Actions\Site;


use Carbon\Carbon;
use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Logo extends BatchAction
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
            $ret = $this->replaceLogo($server->ip,$server->user,$server->pwd,
                $config->fs_catalog,$config->admin_dir,$file->getRealPath());
            if ($ret['code']!=200){
                $errors[] = sprintf('[#%s]%s替换失败：%s',$model->id,$model->domain,$ret['msg']);
                continue;
            }
            $n++;
        }
        $msg = implode("<br>", $errors);
        if ($n) {
            return $this->response()->success(sprintf('修改站点Logo：%s个站点成功，错误信息：%s', $n,$msg ))->refresh();
        }
        return $this->response()->error(sprintf('修改站点Logo失败：%s', $msg));
    }

    /**
     * @param $server_ip
     * @param $server_user
     * @param $server_pwd
     * @param $site_folder
     * @param $admin_dir
     * @param $logo
     * @return array
     */
    protected function replaceLogo($server_ip,$server_user,$server_pwd,$site_folder,$admin_dir,$logo)
    {
        // 替换后台文件
        $remote_file = sprintf("%s/%s/images/logo.gif", $site_folder, $admin_dir);
        try {
            $connection = ssh2_connect($server_ip, 22);
            ssh2_auth_password($connection, $server_user, $server_pwd);
            // 传输到远程
            $start_scp_send = Carbon::now()->format('H:i:s.u');
            if (ssh2_scp_send($connection, $logo, $remote_file, 0644)) {
                Log::info($this->hash.'changeLogo-ssh2-scp-send-time:' . var_export([
                        'start' => $start_scp_send,
                        'diff' => Carbon::now()->diffInMilliseconds($start_scp_send),
                        'end' => Carbon::now()->format('H:i:s.u'),
                        'local'=>$logo,
                        'remote'=>$remote_file,
                    ], true));

            } else {
                return msg_error('Logo图片发送失败');
            }
        } catch (\Exception $e) {
            return msg_error($e->getMessage());
        }
        return msg_success($site_folder);
    }

}