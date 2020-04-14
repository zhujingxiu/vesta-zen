<?php


namespace App\Admin\Controllers;

use App\Models\Ext;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Layout\Content;

class ExtController extends BaseController
{
    protected $header = "扩展";
    protected $model = Ext::class;

    protected function _model_init()
    {
        parent::_model_init();
    }

    public function store()
    {
        return $this->field_create()->store();
    }

    public function update($id)
    {
        return $this->field_edit($id)->update($id);
    }

    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header($this->header . '管理');
            $content->description($this->header . '创建');

            $content->body($this->form());
        });
    }

    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header($this->header . '管理');
            $content->description($this->header . '创建');

            $content->body($this->form()->edit($id));
        });
    }

    protected function form()
    {
        return Admin::form(Ext::class, function (Form $form) {
            $form->file('preview', '文件');
            $form->php('coder', '代码');
            $form->coderPHP('coder1', '代码');
            $form->editor('content', '内容');
        });
    }
}