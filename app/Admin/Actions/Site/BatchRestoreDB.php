<?php

namespace App\Admin\Actions\Site;

use App\Admin\Extensions\Actions\XBatchAction;
use App\Libs\Site\Site;
use App\Models\SiteLanguage;
use Encore\Admin\Admin;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class BatchRestoreDB extends XBatchAction
{
    public $name = '修复数据库';


    public function form()
    {
        $this->checkbox('new_db', ' ')->options([1=>'使用新文件修复']);
        $this->file('db_file', '数据库文件')->readonly();
        $langs = SiteLanguage::status(1)->selectRaw('CONCAT_WS(" ",id,title,code) AS t,code')->pluck('t', 'code');
        $this->select('lang', '设定语言')->options($langs)->help('如果使用的新文件中设定了语言，则可不用再次设定');
        $js =<<<JS
       $('input.new_db').on('ifChecked', function(event){
           $('input.db_file').removeAttr('readonly').removeAttr('disabled')
           .parent().removeClass('disabled').removeAttr('disabled');
       });
       $('input.new_db').on('ifUnchecked', function(event){
           $('input.db_file').attr('readonly','1').attr('disabled','disabled')
           .parent().addClass('disabled').attr('disabled','disabled');
       });
JS;
        Admin::script($js);
    }

    /**
     * @param Collection $collection
     * @param Request $request
     * @return \Encore\Admin\Actions\Response
     */
    public function handle(Collection $collection, Request $request)
    {
        $new_db = $request->get('new_db');
        $lang_code = $request->get('lang');
        $db_file = '';
        $n =0;
        $errors = [];
        if ($new_db){
            if ($request->hasFile('db_file')){
                $ret = $this->validateDBFile($request->file('db_file'));
                if ($ret['code']!=200){
                    $this->response()->error($ret['msg']);
                }
                $db_file = $request->file('db_file')->getRealPath();
            }
        }

        foreach ($collection as $model){
            // 使用本地备份数据库发送到远程服务器恢复
            if (!$db_file){
                $tpl = $model->template;
                $db_file = storage_path(sprintf("%s/%s",rtrim($tpl->path,'/'),$tpl->db_file));
                if (!file_exists($db_file)){
                    $errors[] = sprintf("[#%s]%s:文件%s不存在",$model->id,$model->domain,$db_file);
                    continue;
                }
            }
            $server = $model->server;
            $config = $model->config;
            $site = new Site($server->ip,$server->user,$server->pass);
            $ret = $site->restoreDatabase($db_file,$config->db_name,$config->db_user,$config->db_pass,1);
            if ($ret['code']!=200){
                $errors[] = sprintf("[#%s]%s:文件%s-%s",$model->id,$model->domain,$db_file,$ret['msg']);
                continue;
            }
            if ($lang_code) {
                $ret = $site->setupSite($config->db_name, $config->db_user, $config->db_pass, $lang_code);
                if ($ret['code'] != 200) {
                    $errors[] = sprintf("[#%s]%s:设置%s-%s", $model->id, $model->domain, $lang_code, $ret['msg']);
                    continue;
                }
            }
            $n++;
        }
        if ($n) {
            return $this->response()->success(action_msg($this->name,$n,$errors))->refresh();
        }
        return $this->response()->error(action_msg($this->name,$n,$errors));
    }

    /**
     * @param UploadedFile $file
     * @return array
     */
    protected function validateDBFile(UploadedFile $file)
    {
        if (empty($file) || !$file->isValid()) {
            return msg_error('文件无效，请重新选择文件');
        }
        $size = $file->getClientSize();
        if ($size > 64 * 1024 * 1024) {
            return msg_error('最大支持64M文件大小');
        }
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, ['sql'])) {
            return msg_error('支持文件格式为sql');
        }
        return msg_success('');
    }
}