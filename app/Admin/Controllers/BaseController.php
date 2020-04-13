<?php


namespace App\Admin\Controllers;

use App\Admin\Extensions\Actions\XDropdownActions;
use App\Http\Controllers\Controller;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\Input;
use App\Admin\Extensions\ExcelExpoter;
use App\Admin\Extensions\Tools\Action;
use Encore\Admin\Show;

class BaseController extends Controller
{
    protected $model;

    protected $header = '';
    // 默认列表上不显示create_at字段
    protected $except_create_at = true;

    protected $_url = '';

    protected $field_config = [
        'except_list' => [], //不允许显示在列表的字段 例：['id', 'status']
        'except_create' => [], //不允许创建的字段 例：['id', 'status']
        'except_edit' => [], //不允许编辑的字段 例：['id', 'status']
        'except_show' => [], //不允许查看的字段 例：['id', 'status']
        'except_search' => [], //不允许查询的字段 例：['id', 'status']
        'only_search' => [], //只允许查询的字段 例：['id', 'status']
        'replace' => [], //替换字段 例：['user' => 'sysMembers.cname']
        'file' => [], //文件字段 例：['file']
        'image' => [], //图片字段 例：['image']
        'link' => [], //链接字段 例：['link']
        'default' => [], //创建表单设置默认值字段 例：['link']
        'password' => [], //密码字段 例：['password']
        'input' => [], //显示替换 例：['name' => 'test']
        'extent_list' => [], //列表扩展字段 例： ['sysMembers.cname' => '员工姓名']
        'extent_form' => [], //表单扩展字段 例： ['sysMembers.cname' => '员工姓名']
        'special' => [], //特殊字段 例： ['sysMembers.cname' => '员工姓名']
        'select' => [], // 下拉可视化的翻译字段 例：['status' => ['0' => '禁用', '1' => '启用']]
        'multipleSelect' => [], //多下拉可视化的翻译字段 例：['role' => ['A', 'B', 'C', 'D']
        'load' => [], //下拉需要翻页的翻译字段 例：['cic_clinics_id' => ['trans' => ['cicClinics', 'name'],'url' => '/admin/api/clinics'],
        'sort' => [], // 显示字段重新排序
        'comment' => [] // 自定义字段标题
    ];

    //模型相关属性
    protected $model_fields = [];
    protected $model_conn = [];
    protected $model_table = [];

    public function list_url()
    {
        return url($this->_url);
    }

    public function __construct()
    {
        if (!$this->model_fields) {
            $model = new $this->model;
            $this->model_conn = $model->getConnectionName();
            $this->model_table = $model->getTable();
            $this->model_fields = $this->table_fields($this->model_table, $this->model_conn);
        }

        $this->_model_init();
        $this->_format_comment();
        $this->_except_default();
//        if (isset($this->field_config['except_edit']) && $this->field_config['except_edit']){
//            $this->field_config['except_edit'] = array_merge($this->field_config['except_edit'],[$model->getKeyName()]);
//        }
    }

    // 自定义字段标题
    protected function _format_comment()
    {
        if (isset($this->field_config['comment']) && is_array($this->field_config['comment'])) {
            foreach ($this->field_config['comment'] as $colunm => $comment) {
                if (isset($this->model_fields[$colunm]) && isset($this->model_fields[$colunm]['Comment'])) {
                    $this->model_fields[$colunm]['Comment'] = $comment;
                }
            }
        }
    }

    /**
     * 配置初始化
     */
    protected function _model_init()
    {
        $this->field_config['radio']['status'] = ['0' => '禁用', '1' => '启用'];
    }

    protected function _except_default()
    {
        if ($this->except_create_at && in_array('create_at', array_keys($this->model_fields))) {
            $this->field_config['except_list'] = array_merge($this->field_config['except_list'], ['create_at']);
        }
    }

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header($this->header . '管理');
            $content->description($this->header . '列表');
            config()->set('admin.title', $this->header . '列表');
            $content->body($this->field_list());
        });
    }

    /**
     * Create interface.
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header($this->header . '管理');
            $content->description($this->header . '创建');
            config()->set('admin.title', $this->header . '创建');
            $content->body($this->field_create());
        });
    }

    /**
     * Edit interface.
     * @param int $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header($this->header . '管理');
            $content->description($this->header . '编辑');
            config()->set('admin.title', $this->header . '编辑');
            $content->body($this->field_edit($id)->edit($id));
        });
    }

    /**
     * Show interface.
     * @param int $id
     * @return Content
     */
    public function show($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header($this->header . '管理');
            $content->description($this->header . '查看');
            config()->set('admin.title', $this->header . '查看');
            $content->body($this->field_show($id));
        });
    }

    /**
     * 获取当前数据的字段信息
     * @param $tableName
     * @param $con
     * @return bool
     */
    protected function table_fields($tableName, $con)
    {
        return table_fields($tableName, $con);
    }

    /**
     * 列表显示
     * @return Grid
     */
    protected function field_list()
    {
        return Admin::grid($this->model, function (Grid $grid) {
            $this->setDataFrom($grid);
            $result = $this->model_fields;
            $field_config = $this->field_config;
            $fields = array_except(array_merge(array_merge($result, $field_config['extent_list']), $field_config['special']), $field_config['except_list']);
            if (count($this->field_config['sort']) == count($fields)) {
                array_multisort($arr1, SORT_DESC, $fields);
                array_reverse($fields);
            } else {
                $fields = $this->sortFields($fields, $this->field_config['sort']);
            }
            $this->setGridPreMethods($grid);
            foreach ($fields as $k => $v) {
                if (isset($this->field_config['before']) && array_key_exists($k, $this->field_config['before'])) {
                    $fun_name = $this->field_config['before'][$k];
                    $class_name = get_class($this);
                    if (is_array($fun_name)) {
                        foreach ($fun_name as $func) {
                            call_user_func(array(app($class_name), $func), $grid);
                        }
                    } else {
                        call_user_func(array(app($class_name), $fun_name), $grid);
                    }
                }
                if ($k == 'status') {
                    $grid->column('status', '状态')->using([
                        0 => '禁用',
                        1 => '启用',
                    ], '未知')->dot([
                        0 => 'default',
                        1 => 'success',
                    ])->sortable();
                } else if (array_key_exists($k, $field_config['replace'])) {
                    $grid->column($field_config['replace'][$k], $v['Comment'])->sortable();
                } else if (array_key_exists($k, $field_config['select'])) {
                    $grid->column($k, $v['Comment'])->display(function () use ($k, $field_config) {
                        if (strpos($k, '.')) {
                            $arr = explode('.', $k);
                            $value = $this;
                            foreach ($arr as $_name) {
                                $value = $value->{$_name};
                            }
                        } elseif (!empty($field_config['select'][$k]['self'])) {

                            return '';
                        } else {
                            $value = $this->{$k};
                        }
                        return $field_config['select'][$k][$value] ?: '';
                    })->sortable();
                } else if (array_key_exists($k, $field_config['multipleSelect'])) {
                    $grid->column($k, $v['Comment'])->display(function () use ($k, $field_config) {
                        $str = '';
                        if (is_string($this->{$k})) {
                            $options = explode(',', $this->{$k});
                            foreach ($options as $v) {
                                $str .= $field_config['multipleSelect'][$k][$v] . ' ';
                            }
                        }
                        return $str ?: '';
                    })->sortable();
                } else if (array_key_exists($k, $field_config['input'])) {
                    $grid->column($k, $v['Comment'])->display(function () use ($k, $field_config) {
                        return $field_config['input'][$k] ?: '';
                    })->sortable();
                } else if (array_key_exists($k, $field_config['special'])) {
                    $grid->column($k, $v['Comment'])->display(function () use ($k, $v, $field_config) {
                        list($l1, $l2) = explode('.', $k);
                        $L1 = ucwords(camel_case($l1));
                        $l1 = 'App\\Models\\' . $L1;
                        if (method_exists($l1, '_gird_' . $l2)) {
                            return call_user_func_array([$l1, '_gird_' . $l2], [$this->{$L1}->{$l2}]);
                        }
                        return '';
                    })->sortable();
                } else if (in_array($k, $field_config['image']) || array_key_exists($k, $field_config['image'])) {
                    $grid->column($k, $v['Comment'])->display(function ($value, $column) use ($k, $v, $field_config) {
                        // 是完整的图片链接
                        if (stripos($value, 'http') === 0) {
                            return '<img style="max-width:150px;max-height:150px" class="img img-thumbnail"  src="' . $value . '">' . $value . '</a>';
                        }
                        // 指定字段有单一设置前缀
                        if (array_key_exists($k, $field_config['image'])) {
                            return $column->image($field_config['image'][$k], 110, 110);
                        }
                        // 给所有字段设置了前缀
                        if (!empty($field_config['image']['prefix'])) {
                            return $column->image($field_config['image']['prefix'], 110, 110);
                        }
                        // 用默认前缀
                        return $column->image(asset('/upload'), 110, 110);
                    })->sortable();
                } else if (in_array($k, $field_config['link'])) {
                    $grid->column($k, $v['Comment'])->display(function ($k, $column) {
                        $link = 'http://' . $k;
                        return $column->link($link);
                    })->sortable();
                } else if (in_array($k, $field_config['password'])) {
                    $grid->column($k, $v['Comment'])->display(function ($k, $column) {
                        return '<i class="fa fa-lock"></i> 保密';
                    })->label('default')->copyable()->sortable();
                } else {
                    $grid->column($k, $v['Comment'])->sortable();
                }
                if (isset($this->field_config['after']) && array_key_exists($k, $this->field_config['after'])) {
                    $fun_name = $this->field_config['after'][$k];
                    $class_name = get_class($this);
                    if (is_array($fun_name)) {
                        foreach ($fun_name as $func) {
                            call_user_func(array(app($class_name), $func), $grid);
                        }
                    } else {
                        call_user_func(array(app($class_name), $fun_name), $grid);
                    }
                }
            }
            $this->disableActions($grid);
            $grid->exporter(new ExcelExpoter());
            $this->filters($grid, $result);
            $this->tools($grid);
            $this->setGridMethods($grid);
            $grid->setActionClass(XDropdownActions::class);
            $grid->perPages([10, 20, 30, 50, 100]);
        });
    }

    /**
     * 添加
     * @return Form
     */
    protected function field_create()
    {
        return Admin::form($this->model, function (Form $form) {
            $field_config = $this->field_config;
            $fields = array_except(array_merge($this->model_fields, $field_config['extent_form']), $field_config['except_create']);
            foreach ($fields as $k => $v) {
                if ($k == 'status') {
                    $form->radio('status', $v['Comment'])->options($field_config['select'][$k])->default(1);
                } else if (array_key_exists($k, $field_config['select'])) {
                    $obj = $form->select($k, $v['Comment'])->options($field_config['select'][$k]);
                } else if (array_key_exists($k, $field_config['multipleSelect'])) {
                    $obj = $form->multipleSelect($k, $v['Comment'])->options($field_config['multipleSelect'][$k]);
                } else if (array_key_exists($k, $field_config['load'])) {
                    $obj = $form->select($k, $v['Comment'])->options()
                        ->ajax($field_config['load'][$k]['url']);
                } else if (in_array($k, $field_config['image']) || array_key_exists($k, $field_config['image'])) {
                    $obj = $form->image($k, $v['Comment']);
                    if (isset($field_config['image'][$k])
                        && is_array($field_config['image'][$k])
                        && isset($field_config['image'][$k]['rules'])) {
                        $rules = $field_config['image'][$k]['rules'];
                        $messages = $field_config['image'][$k]['messages'] ?? '';
                    } else {
                        $rules = 'image';
                        $messages = ['image' => '验证的文件必须是一个图像（ jpeg、png、bmp、gif、或 jpg ）'];
                    }
                    $obj->rules($rules, $messages);
                } else if (in_array($k, $field_config['file']) || array_key_exists($k, $field_config['file'])) {
                    $obj = $form->file($k, $v['Comment']);
                    if (isset($field_config['file'][$k])
                        && is_array($field_config['file'][$k])
                        && isset($field_config['file'][$k]['rules'])) {
                        $rules = $field_config['file'][$k]['rules'];
                        $messages = $field_config['file'][$k]['messages'] ?? '';
                    } else {
                        $rules = 'file';
                        $messages = ['file' => '验证的文件必须是一个文件'];
                    }
                    $obj->rules($rules, $messages);
                } else {
                    if (isset($v['Type'])) {
                        switch ($v['Type']) {
                            case 'date':
                                $obj = $form->date($k, $v['Comment']);
                                break;
                            case 'datetime':
                                $obj = $form->datetime($k, $v['Comment']);
                                break;
                            case 'text':
                                $obj = $form->textarea($k, $v['Comment']);
                                break;

                            case 'image':
                                $obj = $form->image($k, $v['Comment']);
                                if (isset($v['Rules'])) {
                                    $obj->rules($v['Rules'], $v['Messages'] ?? []);
                                }
                                if (isset($v['Help'])) {
                                    $obj->help($v['Help']);
                                }
                                break;
                            case 'file':
                                $obj = $form->file($k, $v['Comment']);
                                if (isset($v['Rules'])) {
                                    $obj->rules($v['Rules'], $v['Messages'] ?? []);
                                }
                                if (isset($v['Help'])) {
                                    $obj->help($v['Help']);
                                }
                                break;

                            case 'select':
                                $obj = $form->select($k, $v['Comment']);
                                if (isset($v['Options'])) {
                                    $obj->options($v['Options']);
                                }
                                break;
                            case 'load':
                                $obj = $form->select($k, $v['Comment'])->options()->ajax($v['Ajax']);
                                break;
                            case 'multipleSelect':
                                $obj = $form->multipleSelect($k, $v['Comment']);
                                if (isset($v['Options'])) {
                                    $obj->options($v['Options']);
                                }
                                break;
                            default;
                                $obj = $form->text($k, $v['Comment']);
                                break;
                        }
                    } else {
                        $obj = $form->text($k, $v['Comment']);
                    }
                }
                if ($k == 'status') {
                    $obj->default(1);
                }
                $default_value = empty($field_config['default'][$k]) ? ($v['Default'] ?? '') : $field_config['default'][$k];
                if ($default_value) {
                    $obj->default($default_value);
                }
            }
            $this->setFormMethods($form);
        });
    }

    /**
     * 编辑
     * @param $id
     * @return Form
     */
    protected function field_edit($id)
    {
        return Admin::form($this->model, function (Form $form) use ($id) {
            $result = $this->model_fields;
            $field_config = $this->field_config;
            $fields = array_except(array_merge($result, $field_config['extent_form']), $field_config['except_edit']);
            $data = $form->model()->findOrFail($id);
            $form->hidden('_method', 'PUT')->default('PUT');
            $form->ignore('_method');
            foreach ($fields as $k => $v) {
                $pk = app($this->model)->getKeyName();
                if ($k == $pk) {
                    $form->display($k, ucwords($k))->value($v);
                } else if ($k == 'status') {
                    $form->radio('status', $v['Comment'])->options($field_config['select'][$k])->default($data[$k]);
                } else if (array_key_exists($k, $field_config['select'])) {
                    $form->select($k, $v['Comment'])->options($field_config['select'][$k])->default($data[$k]);
                } else if (array_key_exists($k, $field_config['multipleSelect'])) {
                    $form->multipleSelect($k, $v['Comment'])->options($field_config['multipleSelect'][$k])->default(explode(',', $data[$k]));
                } else if (array_key_exists($k, $field_config['load'])) {
                    $form->select($k, $v['Comment'])
                        ->options([$data[$k] => $data->{$field_config['load'][$k]['trans'][0]}->{$field_config['load'][$k]['trans'][1]}])
                        ->ajax($field_config['load'][$k]['url'])
                        ->default($data[$k]);
                } else if (in_array($k, $field_config['image']) || array_key_exists($k, $field_config['image'])) {
                    $obj = $form->image($k, $v['Comment'])->default(upload_prefix($data[$k]) . $data[$k]);
                    if (isset($field_config['image'][$k])
                        && is_array($field_config['image'][$k])
                        && isset($field_config['image'][$k]['rules'])) {
                        $rules = $field_config['image'][$k]['rules'];
                        $messages = $field_config['image'][$k]['messages'] ?? '';
                    } else {
                        $rules = 'image';
                        $messages = ['image' => '验证的文件必须是一个图像（ jpeg、png、bmp、gif、或 jpg ）'];
                    }
                    $obj->rules($rules, $messages);
                } else if (in_array($k, $field_config['file']) || array_key_exists($k, $field_config['file'])) {
                    $obj = $form->file($k, $v['Comment'])->default(upload_prefix($data[$k]) . $data[$k]);
                    if (isset($field_config['file'][$k])
                        && is_array($field_config['file'][$k])
                        && isset($field_config['file'][$k]['rules'])) {
                        $rules = $field_config['file'][$k]['rules'];
                        $messages = $field_config['file'][$k]['messages'] ?? '';
                    } else {
                        $rules = 'file';
                        $messages = ['file' => '验证的文件必须是一个文件'];
                    }
                    $obj->rules($rules, $messages);
                } else {
                    if (isset($v['Type'])) {
                        switch ($v['Type']) {
                            case 'date':
                                $form->date($k, $v['Comment'])->default($data[$k]);
                                break;
                            case 'datetime':
                                $form->datetime($k, $v['Comment'])->default($data[$k]);
                                break;
                            case 'text':
                                $form->textarea($k, $v['Comment'])->default($data[$k]);
                                break;
                            case 'image':
                                $obj = $form->image($k, $v['Comment']);
                                if (isset($v['Rules'])) {
                                    $obj->rules($v['Rules'], $v['Messages'] ?? []);
                                }
                                if (isset($v['Help'])) {
                                    $obj->help($v['Help']);
                                }
                                if (isset($data[$k]) && $data[$k]) {
                                    $obj->default(upload_prefix($data[$k]) . $data[$k]);
                                }
                                break;
                            case 'file':
                                $obj = $form->file($k, $v['Comment']);
                                if (isset($v['Rules'])) {
                                    $obj->rules($v['Rules'], $v['Messages'] ?? []);
                                }
                                if (isset($v['Help'])) {
                                    $obj->help($v['Help']);
                                }
                                if (isset($data[$k]) && $data[$k]) {
                                    $obj->default(upload_prefix($data[$k]) . $data[$k]);
                                }
                                break;

                            case 'select':
                                $obj = $form->select($k, $v['Comment'])
                                    ->options($v['Options'])
                                    ->default($data[$k]);
                                break;
                            case 'load':
                                $obj = $form->select($k, $v['Comment'])
                                    ->options($v['Options'])
                                    ->ajax($v['Ajax'])
                                    ->default($data[$k]);;
                                break;
                            case 'multipleSelect':
                                $obj = $form->multipleSelect($k, $v['Comment'])
                                    ->options($v['Options'])
                                    ->default(explode(',', $data[$k]));;

                                break;
                            default;
                                $form->text($k, $v['Comment'])->default($data[$k]);
                                break;
                        }
                    } else {
                        $form->text($k, $v['Comment'])->default($data[$k]);
                    }
                }
            }
            $this->setFormMethods($form);
        });
    }

    /**查看
     * @param $id
     * @return Show
     */
    protected function field_show($id)
    {
        return Admin::show(app($this->model)::findOrFail($id), function (Show $show) {
            $result = $this->model_fields;
            $field_config = $this->field_config;
            $fields = array_except(array_merge($result, $field_config['extent_list']), $field_config['except_show']);
            foreach ($fields as $k => $v) {
                if (in_array($k, $field_config['image']) || array_key_exists($k, $field_config['image'])) {
                    $show->{$k}($v['Comment'])->unescape()->as(function ($value) {
                        $img = upload_prefix($value);
                        return "<img style='max-width: 150px;max-height: 150px' class='img img-thumbnail' src='{$img}' />";
                    });;
                } else {
                    $show->{$k}($v['Comment']);
                }
            }
            $show->panel()->tools(function ($tools) {
                $tools->disableEdit();
                $tools->disableDelete();
            });
        });
    }

    /**
     * 设置表单扩展方法
     * @param $form
     */
    protected function setFormMethods($form)
    {
    }

    /**
     * 数据权限筛选基础
     * @param $grid
     */
    protected function setDataFrom($grid)
    {

        //默认显示有效
        $status_val = Input::get('status');
        $grid->model()->orderBy(app($this->model)->getKeyName(), 'desc')
            ->where(function ($q) use ($status_val) {
//                if ($status_val !== '0')
//                    $q->status(1);
            });

    }

    /**
     * 设置grid方法（之前）
     * @param $grid
     */
    protected function setGridPreMethods($grid)
    {
    }

    /**
     * 设置grid方法（之后）
     * @param $grid
     */
    protected function setGridMethods($grid)
    {
    }

    /**
     * @param $grid
     */
    protected function disableActions($grid)
    {
        $grid->disableActions();
    }

    /**
     * grid tools
     * @param $grid
     */
    protected function tools($grid)
    {
        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
                //$batch->add('', new Common(''));
                $batch->add('查看', new Action(''));
                $this->batch($batch);
            });
            $this->toolForSearch($tools);
            $this->tool($tools);
        });
    }

    /**
     * grid tool
     * @param $tools
     */
    protected function tool(Grid\Tools $tools)
    {
    }

    protected function toolForSearch($tools)
    {
        //$tools->append(SearchModal::execute($this->model_fields));
    }

    /**
     * grid batch
     * @param $batch
     */
    protected function batch($batch)
    {
        $batch->add('编辑', new Action('/edit'));
    }

    /**
     * 过滤器 filters
     * @param $grid
     * @param $fields
     */
    protected function filters($grid, $fields)
    {
        $grid->filter(function ($filter) use ($fields) {

            $this->filter($filter);

            $field_config = $this->field_config;
            if ($field_config['only_search']) {
                $fields = array_only(array_merge($fields, $field_config['extent_list']), $field_config['only_search']);
            } else {
                $fields = array_except(array_merge($fields, $field_config['extent_list']), array_merge($field_config['except_search'], [app($this->model)->getKeyName()]));
            }

            foreach ($fields as $k => $v) {

                if (array_key_exists($k, $field_config['select'])) {
                    $filter->equal($k, $v['Comment'])->select($field_config['select'][$k]);
                } else {
                    if (isset($v['Type'])) {
                        $type = (strpos($v['Type'], '(') !== false) ? substr($v['Type'], 0, strpos($v['Type'], '(')) : $v['Type'];
                        switch ($type) {
                            case 'date':
                                $filter->between($k, $v['Comment'])->date();
                                break;
                            case 'datetime':
                                $filter->between($k, $v['Comment'])->datetime();
                                break;
                            case 'text':
                            case 'varchar':
                            case 'char':
                                $filter->like($k, $v['Comment']);
                                break;
                            default;
                                $filter->equal($k, $v['Comment']);
                                break;
                        }
                    } else {
                        $filter->like($k, $v['Comment']);
                    }
                }
            }

        });
    }

    /**
     * 过滤器 filter
     * @param $filter
     */
    protected function filter($filter)
    {

    }

    /**
     * 字段排序
     * @param $fields
     * @param $sort
     * @return array
     */
    public function sortFields($fields, $sort)
    {
        $new_fields = [];
        foreach ($sort as $item) {
            $new_fields[$item] = $fields[$item];
        }
        return $new_fields ?: $fields;
    }

    public function resource($slice = -2)
    {
        $segments = explode('/', trim(app('request')->getUri(), '/'));

        if ($slice != 0) {
            $segments = array_slice($segments, 0, $slice);
        }

        return implode('/', $segments);
    }

    /**
     * 返回地址栏参数 过滤空值
     * @param array $except
     * @return array
     */
    protected function getInputs($except = ['per_page', '_pjax', '_export_'])
    {
        $inputs = Input::except($except);
        $status = $inputs['status'] ?? false;
        foreach ($inputs as &$input) {
            if (is_array($input)) $input = array_filter($input);
        }
        $inputs = array_filter($inputs);
//        if ($status === '0') {
//            $inputs['status'] = 0;
//        }
        return $inputs;
    }
}