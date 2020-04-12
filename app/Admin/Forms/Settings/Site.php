<?php

namespace App\Admin\Forms\Settings;

use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;

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
        //dump($request->all());

        admin_success('Processed successfully.');

        return back();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->text('site_catalog', 'zencart本地路径')->rules('required');
        $this->text('site_db_user', '数据库默认用户名')->rules('required');
        $this->text('site_db_pass', '数据库默认用户密码')->rules('required');
    }

    /**
     * The data of the form.
     *
     * @return array $data
     */
    public function data()
    {
        return [
            'site_catalog' => './site/zencart',
            'site_db_user' => 'site_zencart_hz',
            'site_db_pass' => '',
        ];
    }
}
