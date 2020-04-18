<?php


namespace App\Admin\Controllers;

use App\Events\ServiceCacheEvent;
use App\Models\SiteLanguage;
use App\Models\SiteTemplate;
use App\Repositories\SiteTemplateRepository;
use Chumper\Zipper\Zipper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SiteTemplateController extends BaseController
{
    protected $header = "站点模板";
    protected $model = SiteTemplate::class;
    protected $_url = '/admin/site-templates';

    protected $repository;

    public function __construct(SiteTemplateRepository $siteTemplateRepository)
    {
        parent::__construct();
        $this->repository = $siteTemplateRepository;
    }

    protected function _model_init()
    {
        parent::_model_init();
        $this->field_config['image'] = ['preview'];
        $this->field_config['default'] = ['admin_dir' => config('site.site_admin'), 'db_file' => config('site.site_sql')];
        $this->field_config['except_create'] = ['sites', 'admin_id', 'status', 'path'];
        $this->field_config['except_edit'] = ['create_at', 'update_at'];
        $this->field_config['extent_form'] = ['languages' => [
            'Comment' => '包含语言',
            'Type' => 'multipleSelect',
            'Options' => SiteLanguage::status(1)->selectRaw('id,CONCAT_WS(" ",id, title,code) AS t')->pluck('t', 'id'),
        ], 'file' => [
            'Comment' => '上传模板文件',
            'Type' => 'file',
            'Help' => <<<EOT
                1.文件格式： .zip<br>
                2.文件大小：最大64M<br>
                3.上传zip压缩模板会自动解压到指定目录，并修改模板路径<br>
                4.校验填写的后台目录及数据库文件是否有效<br>
EOT
        ]];
        $this->field_config['file'] = ['help' => 'sss'];
        $this->field_config['after'] = ['path' => ['columnLanguages']];
    }


    public function columnLanguages($grid)
    {
        $grid->column('languages', '包含语言')->display(function () {
            $lang = [];

            foreach ($this->languages as $v) {
                $lang[] = '[#' . $v->id . ']' . $v->title;
            }
            return implode(' <br> ', $lang);
        });
    }

    /**
     * @param UploadedFile $file
     * @return array
     */
    protected function validateZip(UploadedFile $file)
    {
        if (empty($file) || !$file->isValid()) {
            return msg_error('文件无效，请重新选择文件');
        }
        $size = $file->getClientSize();
        if ($size > 64 * 1024 * 1024) {
            return msg_error('最大支持64M文件大小');
        }
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, ['zip'])) {
            return msg_error('支持文件格式为zip');
        }
        return msg_success('');
    }

    protected function uploadZip(UploadedFile $file, $name, $admin_dir = null)
    {
        $ret = $this->validateZip($file);
        if ($ret['code'] != 200) {
            return $ret;
        }
        try {
            $site_folder = sprintf('app/public/sites/%s/%s_%s', date('ymd'), date('His') . str_random(4), $name);
            $realpath = storage_path($site_folder);
            if (!is_dir($realpath)) {
                mkdir($realpath, 0755, true);
            }
            $zipper = new Zipper();
            $zipper->make($file)->extractTo($realpath);
            $zipper->close();
            if ($admin_dir && !is_dir($realpath . '/' . $admin_dir)) {
                return msg_error('后台目录不存在');
            }
        } catch (\Exception $e) {
            Log::info('uploadZip:' . $e->getMessage() . 'dir:' . $site_folder . '|realpath:' . $realpath);
            return msg_error( $e->getMessage() );
        }
        return msg_success('', ['path' => $site_folder]);
    }

    /**
     * @param array $request
     * @return array
     */
    protected function validateParams($request)
    {
        if (empty($request['name'])) {
            return msg_error('名称必填');
        }
        if (preg_match('/[\x{4e00}-\x{9fa5}]/u', $request['name']) > 0) {
            return msg_error('名称不能含有中文');
        }
        if (empty($request['admin_dir'])) {
            return msg_error('后台目录必填');
        }
        if (preg_match('/[\x{4e00}-\x{9fa5}]/u', $request['admin_dir']) > 0) {
            return msg_error('后台目录名称不能含有中文');
        }
        if (empty($request['db_file'])) {
            return msg_error('数据库文件必填');
        }
        if (preg_match('/[\x{4e00}-\x{9fa5}]/u', $request['db_file']) > 0) {
            return msg_error('数据库文件名称不能含有中文');
        }
        return msg_success();
    }

    public function store(Request $request)
    {
        $ret = $this->validateParams($request->all());
        if ($ret['code'] != 200) {
            admin_toastr($ret['msg'], 'error');
            return back()->withInput();
        }
        if ($request->hasFile('preview')) {
            $ret = upload_image($request->file('preview'));
            if ($ret['code'] != 200) {
                admin_toastr($ret['msg'], 'error');
                return back()->withInput();
            }
        } else {
            admin_toastr('预览图必须', 'error');
            return back()->withInput();
        }
        $preview = $ret['data']['path'];
        $name = $request->get('name');
        $admin_dir = $request->get('admin_dir');
        $db_file = $request->get('db_file');
        if ($request->hasFile('file')) {
            $file = $request->file("file");
            $ret = $this->uploadZip($file, $name, $admin_dir);
            if ($ret['code'] != 200) {
                admin_toastr($ret['msg'], 'error');
                return back()->withInput();
            }
        } else {
            admin_toastr('模板压缩文件必须上传', 'error');
            return back()->withInput();
        }

        $path = $ret['data']['path'];
        $author = $request->get('author');
        $remark = $request->get('remark');
        $languages = $request->get('languages');
//        $this->field_config['extent_form'] = ['path'=>['Comment'=>'路径']];
//        $this->field_config['default'] = ['path'=>$ret['msg']];
//        unset($this->field_config['extent_form']['file']);
//        $field_config = $this->field_config;
//        $fields = array_except(array_merge($this->model_fields, $field_config['extent_form']), $field_config['except_create']);
//        dd($fields,$this->field_create());
//        return $this->field_create()->store();
        $ret = $this->repository->addTemplate($name, $author, $preview, $path, $admin_dir, $db_file, $remark, $languages);
        if ($ret['code'] != 200) {
            admin_toastr($ret['msg'], 'error');
            return back()->withInput();
        }
        admin_toastr($ret['msg']);
        return redirect($this->list_url());
    }

    public function update($id, Request $request)
    {
        $ret = $this->validateParams($request->all());
        if ($ret['code'] != 200) {
            admin_toastr($ret['msg'], 'error');
            return back()->withInput();
        }
        $preview = '';
        if ($request->hasFile('preview')) {
            $ret = upload_image($request->file('preview'));
            if ($ret['code'] != 200) {
                admin_toastr($ret['msg'], 'error');
                return back()->withInput();
            }
            $preview = $ret['data']['path'];
        }
        $name = $request->get('name');
        $path = $request->get('path');
        $admin_dir = $request->get('admin_dir');
        $db_file = $request->get('db_file');
        if ($request->hasFile('file')) {
            $file = $request->file("file");
            $ret = $this->uploadZip($file, $name, $admin_dir);
            if ($ret['code'] != 200) {
                admin_toastr($ret['msg'], 'error');
                return back()->withInput();
            }
            $path = $ret['data']['path'];
        }
        $author = $request->get('author');
        $remark = $request->get('remark');
        $status = $request->get('status');
        $languages = $request->get('languages');
        $ret = $this->repository->editTemplate($id, $name, $author, $preview, $path, $admin_dir, $db_file, $remark, $status, $languages);
        if ($ret['code'] != 200) {
            admin_toastr($ret['msg'], 'error');
            return back()->withInput();
        }

        admin_toastr($ret['msg']);
        return redirect($this->list_url());
    }

    public function apiTemplates(Request $request)
    {
        $langId = $request->get('q');

        $arr = $this->repository->getTemplatesByLangId($langId);

        $options = [['id' => ' ', 'text' => '请选择']];
        foreach ($arr as $v) {
            if (!$v->tpl_id) {
                continue;
            }
            $t = $v->templates;
            if (!$t || !$t->name){
                continue;
            }
            $languages = [];
            if ($t->languages) {
                foreach ($t->languages as $lang) {
                    $languages[] = $lang->title;
                }
            }
            $options[] = [
                'id' => $v->tpl_id,
                'preview' => $t->preview ? upload_prefix($t->preview) : '',
                'text' => sprintf("%s-使用次数%s-%s[%s-%s]", $t->name, $t->sites, implode("/", $languages), $t->author, $t->create_at)
            ];
        }
        return msg_success('', $options);

    }
}