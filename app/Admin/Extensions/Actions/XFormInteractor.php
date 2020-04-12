<?php


namespace App\Admin\Extensions\Actions;


use App\Admin\Extensions\Coder\CoderPHP;
use App\Admin\Extensions\LoadSelect;
use Encore\Admin\Actions\Action;
use Encore\Admin\Actions\Interactor\Form;

class XFormInteractor extends Form
{
    public function __construct(Action $action)
    {
        parent::__construct($action);
        self::$elements[] = 'loadSelect';
        self::$elements[] = 'coderPHP';
    }

    /**
     * @param string $column
     * @param string $label
     *
     * @return LoadSelect
     */
    public function loadSelect($column, $label = '')
    {
        $field = new LoadSelect($column, $this->formatLabel($label));

        $this->addField($field);

        return $field;
    }

    /**
     * @param $column
     * @param string $label
     * @return CoderPHP
     */
    public function coderPHP($column, $label = '')
    {
        $field = new CoderPHP($column, $this->formatLabel($label));
        $this->addField($field);

        return $field;
    }
}