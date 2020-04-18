<?php

namespace App\Admin\Forms\Settings;

use App\Libs\Site\ZenCart\ZenCart;
use App\Models\Setting;
use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class Site extends Form
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = '站点配置';

    /**
     * Handle the form request.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request)
    {
        foreach ($request->except('_token','_method') as $key => $value){
            redis_set(gen_cache_key('site.'.$key),$value);
            Setting::updateOrCreate(['tab'=>'site','conf_key'=>$key],[
                'conf_value'=>$value,
                'json'=>0
            ]);
        }

        admin_success('更新成功.');

        return back();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->text('dir_copy', 'ZenCart本地副本路径')->rules('required')
            ->readonly()->help('添加站点时，创建的站点预压缩目录。须有读写权限');
        $this->text('dir_admin', 'ZenCart默认后台目录名')->rules('required');
        $this->text('db_file', 'ZenCart默认数据库文件')->rules('required');
        $this->text('tpl_preview', 'ZenCart模板预览图片名')->rules('required')
            ->help('模板预览的图片文件名称： TEMPLATE/images/scr_template_default.jpg');

        $this->text('db_user', 'ZenCart数据库默认用户')->rules('required');
        $this->password('db_pass', 'ZenCart数据库默认密码')->rules('required');
    }

    /**
     * The data of the form.
     *
     * @return array $data
     */
    public function data()
    {
        return [
            'dir_copy' => \App\Libs\Site\Site::SiteCopy(),
            'dir_admin' => ZenCart::AdminDir(),
            'db_file' => ZenCart::DBFile(),
            'tpl_preview' => ZenCart::PreviewImg(),
            'db_user' => ZenCart::DBUser(),
            'db_pass' => ZenCart::DBPass(),
        ];
    }
}
