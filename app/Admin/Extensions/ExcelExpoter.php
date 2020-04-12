<?php

namespace App\Admin\Extensions;

use Encore\Admin\Grid;
use Encore\Admin\Grid\Exporters\AbstractExporter;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Arr;
use Closure;

class ExcelExpoter extends AbstractExporter
{
    protected $file_name = '';
    protected $specialCallBack = [];

    public function __construct(Grid $grid = null, $file_name = null)
    {
        $this->file_name = $file_name;
        parent::__construct($grid);
    }

    public function export()
    {
        $file_name = $this->file_name ?: date("Y-m-d") . '-' . $this->getTable();
        Excel::create($file_name, function ($excel) use ($file_name) {
            $model_name = get_class($this->grid->model()->eloquent());
            $excel->sheet($file_name, function ($sheet) use ($model_name) {
                $name = [];
                $label = [];
                foreach ($this->grid->columns() as $k => $v) {
                    $name[] = $v->getName();
                    $label[] = $v->getLabel();
                }
                $data = $this->getData();
                $sheet->row(1, $label);
                $rows = [];
                foreach ($data as $k => $v) {
                    // 处理特殊数据
                    foreach ($this->specialCallBack as $n => $call) {
                        if ($call instanceof Closure) {
                            $v[$n] = call_user_func($call, $v);
                        }
                    }
                    $rows[] = array_only(array_dot($this->sanitize($v)), $name);
                }
                $rows = $this->sort_arr($rows, $name, $model_name);
                $this->rows($sheet, $rows);

                ob_end_clean();
            });
        })->export('xls');
    }

    /**
     * 复写worksheet rows 的方法
     * add by zh
     */
    protected function rows(&$sheet, $rows = [])
    {
        $startRow = '2'; // 默认第一行位 title 从第二行开始
        foreach ($rows as $row) {
            $this->row($startRow, $row, $sheet);
            $startRow++;
        }

        return $sheet;
    }

    protected function row($rowNumber, $values, &$sheet)
    {
        $column = 'A';
        foreach ($values as $rowValue) {
            $cell = $column . $rowNumber;
            //长数字转换成字符串
            if (is_numeric($rowValue) && strlen($rowValue) > 11) {
                $sheet->setCellValueExplicit($cell, $rowValue);
            } elseif (is_array($rowValue)) {
                $sheet->setCellValue($cell, $rowValue[0]);
            } else {
                $sheet->setCellValue($cell, $rowValue);
            }
            /*if ($explicit) {
                $this->setCellValueExplicit($cell, $rowValue);
            } else {
                $this->setCellValue($cell, $rowValue);
            }*/
            $column++;
        }
        return $sheet;
    }

    /**
     * Remove indexed array.
     *
     * @param array $row
     *
     * @return array
     */
    protected function sanitize(array $row)
    {
        return collect($row)->reject(function ($val) {
            return is_array($val) && !Arr::isAssoc($val);
        })->toArray();
    }

    /**
     * @param $arr
     * @param $keys
     * @param $model_name
     * @return array
     */
    public function sort_arr($arr, $keys, $model_name)
    {
        $new_arr = [];
        foreach ($arr as $k => $v) {
            foreach ($v as $kk => $vv) {
                if (stripos($kk, '.')) {
                    list($l1, $l2) = explode('.', $kk);
                    $l1 = 'App\\Models\\' . ucwords(camel_case($l1));
                    if (method_exists($l1, '_gird_' . $l2)) {
                        $v[$kk] = call_user_func_array([$l1, '_gird_' . $l2], [$vv]);
                    }
                } else {
                    if ($kk != 'id') {
                        if (method_exists($model_name, '_gird_' . $kk)) {
                            $v[$kk] = call_user_func_array([$model_name, '_gird_' . $kk], [$vv]);
                        }
                    }
                }
            }
            foreach ($keys as $kk => $vv) {
                $new_arr[$k][$vv] = $v[$vv];

                //导出对接数据时用的特例
                if ($vv == 'age_type') {
                    $new_arr[$k][$vv] = '岁';
                }
            }
        }
        //dd($new_arr);
        return $new_arr;
    }

    public function setSpecial($name, Closure $callable)
    {
        $this->specialCallBack[$name] = $callable;
        return $this;
    }

    public static function execute($file_name = null)
    {
        $s = new self();
        if ($file_name) {
            $s->file_name = $file_name;
        }
        return $s;
    }

}