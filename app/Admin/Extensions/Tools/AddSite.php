<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;

class AddSite extends AbstractTool
{
    protected $view = 'admin.extensions.add-site';

    protected $label;

    protected $modal_name;

    protected $form_action;

    protected $data;

    public function __construct($label, $modal_name, $form_action, $data = [])
    {
        $this->label = $label;
        $this->modal_name = $modal_name;
        $this->form_action = $form_action;
        $this->data = $data;

    }


    /**
     * Render CreateButton.
     *
     * @return string
     */
    public function render()
    {

        return view($this->view)->with([
            'label' => $this->label,
            'modal_name' => $this->modal_name,
            'form_action' => $this->form_action,
            'data' => $this->data
        ]);
    }
}
