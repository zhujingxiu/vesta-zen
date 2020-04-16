<?php


namespace App\Admin\Controllers;


use App\Models\AdminUsers;
use App\Models\Worker;
use Encore\Admin\Controllers\UserController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class WorkerController extends UserController
{
    protected function grid()
    {
        $userModel = config('admin.database.users_model');

        $grid = new Grid(new $userModel());

        $grid->column('id', 'ID')->sortable();
        $grid->column('no', '工号')->display(function (){
            return $this->worker->no;
        });
        $grid->column('username', trans('admin.username'));
        $grid->column('name', trans('admin.name'));
        $grid->column('roles', trans('admin.roles'))->pluck('name')->label();
        $grid->column('gender', '性别')->display(function (){
            return $this->worker->gender;
        })->using(Worker::_gird_gender_all());
        $grid->column('phone', '电话')->display(function (){
            return $this->worker->phone;
        });

        $grid->column('join_day', '入职日期')->display(function (){
            return $this->worker->join_day;
        });
        $grid->column('left_day', '状态')->display(function (){
            if (is_null($this->worker->left_day)){
                return '在职';
            }
            return sprintf('于%s离职：',$this->worker->left_day);
        });
        $grid->column('updated_at', trans('admin.updated_at'));

        $grid->actions(function (Grid\Displayers\Actions $actions) {
            if ($actions->getKey() == 1) {
                $actions->disableDelete();
            }
        });

        $grid->tools(function (Grid\Tools $tools) {
            $tools->batch(function (Grid\Tools\BatchActions $actions) {
                $actions->disableDelete();
            });
        });

        return $grid;
    }
    protected function detail($id)
    {
        $show = parent::detail($id);

        $show->worker('员工信息',function ($worker){
            $worker->setResource('/admin/workers');
            $worker->no('工号');
            $worker->gender('性别')->using(Worker::_gird_gender_all());
            $worker->phone('电话');
            $worker->birthday('生日');
            $worker->join_day('入职日期');
            $worker->left_day('状态')->as(function ($val){
                if (is_null($val)){
                    return '在职';
                }
                return sprintf('于%s离职：',$val);
            });
            $worker->panel()
                ->tools(function ($tools) {
                    $tools->disableEdit();
                    $tools->disableList();
                    $tools->disableDelete();
                });;
        });

        return $show;
    }
    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        $form = parent::form();
        if (request('user')!=1) {
            $form->column(1/2,function ($form){

            });

            $form->column(1/2,function ($form){
                $form->text('worker.no', '工号')->rules('required');
                $form->radio('worker.gender', '性别')->options(Worker::_gird_gender_all());
                $form->text('worker.phone', '电话');
                $form->date('worker.birthday', '生日');
                $form->date('worker.join_day', '入职日期')->default(now());
                $form->date('worker.left_day', '离职日期');
            });

        }
        return $form;
    }

}