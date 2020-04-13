<?php


namespace App\Admin\Extensions\Actions;

use Encore\Admin\Actions\BatchAction;

class XBatchAction extends BatchAction
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

}