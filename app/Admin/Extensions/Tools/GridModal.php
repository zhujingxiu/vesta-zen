<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;
use Encore\Admin\Widgets\Form;

class GridModal extends AbstractTool
{
    protected $view = 'admin.extensions.grid-modal';

    protected $label;

    protected $modal_name;

    protected $form_body;

    protected $script;

    public function __construct($label, $modal_name, $form_body, $script = null)
    {
        $this->label = $label;
        $this->modal_name = $modal_name;
        $this->form_body = $form_body;
        $this->script = $script;

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
            'form_body' => $this->form_body,
            'script' => $this->script
        ]);
    }
}
