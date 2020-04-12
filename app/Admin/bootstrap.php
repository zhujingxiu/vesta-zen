<?php

/**
 * Laravel-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */

use Encore\Admin\Form;
use App\Admin\Extensions\CoderPHP;
use App\Admin\Extensions\XFile;
use App\Admin\Extensions\MultipleSelects;
use App\Admin\Extensions\WangEditor;

Form::forget(['map', 'editor']);

Form::extend('file', XFile::class);
Form::extend('coder', CoderPHP::class);
Form::extend('editor', WangEditor::class);
Form::extend('multipleSelects', MultipleSelects::class);

Admin::js('/vendor/laravel-admin/AdminLTE/plugins/select2/select2.full.min.js');
Admin::js('/vendor/laravel-admin/AdminLTE/plugins/select2/i18n/zh-CN.js');
//app('view')->prependNamespace('admin', resource_path('views/encore'));


Admin::js('js/common.js');
