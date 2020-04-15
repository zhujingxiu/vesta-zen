<?php
namespace App\Admin\Extensions\Actions;

use Encore\Admin\Actions\Interactor\Form;
use Encore\Admin\Admin;
class XFormInteractor extends Form
{
    public function addModalHtml()
    {
        if (!method_exists($this->action, 'xForm')) {
            parent::addModalHtml();
        } else {
            $form = new \Encore\Admin\Widgets\Form();
            $form->method('POST');
            $data = [
                'form_body' => $this->action->xForm($form),
                'title' => $this->action->name(),
                'modal_id' => $this->getModalId(),
            ];
            $modal = view('admin.extensions.xmodal', $data)->render();

            Admin::html($modal);
        }
    }

}