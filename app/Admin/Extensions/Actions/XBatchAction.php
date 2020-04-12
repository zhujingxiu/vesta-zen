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

    /**
     * @return string
     */
    public function renders()
    {
        $this->addScript();
dd($this);
        $modalId = '';

        if ($this->interactor instanceof XFormInteractor) {
            $modalId = $this->interactor->getModalId();

            if ($content = $this->html()) {
                return $this->interactor->addElementAttr($content, $this->selector);
            }
        }

        return sprintf(
            "<a href='javascript:void(0);' class='%s' %s>%s</a>",
            $this->getElementClass(),
            $modalId ? "modal='{$modalId}'" : '',
            $this->name()
        );
    }
}