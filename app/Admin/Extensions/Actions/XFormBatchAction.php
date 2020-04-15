<?php
namespace App\Admin\Extensions\Actions;

use Encore\Admin\Actions\BatchAction;
use Encore\Admin\Facades\Admin;
class XFormBatchAction extends BatchAction
{
    protected function initInteractor()
    {
        parent::initInteractor();
        if ($hasForm = method_exists($this, 'form')) {
            $this->interactor = new XFormInteractor($this);
        }
    }

    public function form()
    {

    }

    public function handleActionPromise()
    {
        $resolve = <<<'SCRIPT'
        var actionResolverss = function (data) {
            $('.modal-footer').show()
            $.admin.swal.close();
            var response = data[0];
            var target   = data[1];

            if (typeof response !== 'object') {
                return $.admin.swal({type: 'error', title: 'Oops!'});
            }

            var then = function (then) {
                if (then.action == 'refresh') {
                    $.admin.reload();
                }

                if (then.action == 'download') {
                    window.open(then.value, '_blank');
                }

                if (then.action == 'redirect') {
                    $.admin.redirect(then.value);
                }
            };

            if (typeof response.html === 'string') {
                target.html(response.html);
            }

            if (typeof response.swal === 'object') {
                $.admin.swal(response.swal);
            }

            if (typeof response.toastr === 'object') {
                $.admin.toastr[response.toastr.type](response.toastr.content, '', response.toastr.options);
            }

            if (response.then) {
              then(response.then);
            }
        };

        var actionCatcherss = function (request) {
            $('.modal-footer').show()
            $.admin.swal.close();

            if (request && typeof request.responseJSON === 'object') {
                $.admin.toastr.error(request.responseJSON.message, '', {positionClass:"toast-bottom-center", timeOut: 10000}).css("width","500px")
            }
        };
SCRIPT;

        Admin::script($resolve);

        return <<<'SCRIPT'
         $('.modal-footer').hide()
         $.admin.swal('请求处理中，请耐心等待结果不要关闭窗口！')
        process.then(actionResolverss).catch(actionCatcherss);
SCRIPT;
    }

}