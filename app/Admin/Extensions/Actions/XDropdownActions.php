<?php

namespace App\Admin\Extensions\Actions;

use Encore\Admin\Grid\Displayers\DropdownActions;

class XDropdownActions extends DropdownActions
{
    /**
     * @param null|\Closure $callback
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function display($callback = null)
    {
        $this->addScript();

        if ($callback instanceof \Closure) {
            $callback->call($this, $this);
        }

        if ($this->disableAll) {
            return '';
        }

        $this->prependDefaultActions();

        $actions = [
            'default' => $this->default,
            'custom' => $this->custom,
        ];

        if (!$this->default && count($this->custom) > 3) {

            $actions = [
                'default' => array_slice($this->custom, 0, 3),
                'custom' => array_slice($this->custom, 3),
            ];
        } else if (count($this->custom) <= 3) {
            $actions = [
                'default' => $this->custom,
                'custom' => [],
            ];
        }
        //return view('admin::grid.dropdown-actions', $actions);
        return view('admin.extensions.xdropdown-actions', $actions);
    }
}