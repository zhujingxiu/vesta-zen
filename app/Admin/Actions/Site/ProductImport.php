<?php

namespace App\Admin\Actions\Site;

use App\Libs\Site\ZenCart\ImportProduct;
use Carbon\Carbon;
use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductImport extends BatchAction
{
    public $name = '导入产品数据';
    protected $selector = '.product-import';

    public function html()
    {
        return <<<HTML
        <a class="btn btn-sm btn-default product-import">导入产品数据</a>
HTML;
    }

    public function form()
    {
        $this->file("product", "产品数据文件")->help('.csv文件的产品数据');
    }

    public function handle(Collection $collection, Request $request)
    {
        $hash =  str_random(16).'==';
        $start = Carbon::now()->format('H:i:s.u');
        log_trace_millisecond($hash,$start);
        if (!$request->hasFile('product')) {
            return $this->response()->error('请上传文件');
        }
        $file = $request->file('product');
        $extension = $file->getClientOriginalExtension(); //获取上传图片的后缀名
        if (!in_array(strtolower($extension), ['xls', 'xlsx', 'csv'])) {
            return $this->response()->error('excel格式只允许csv,xls或者xlsx.');
        }
        $rows = $this->readFile($file->getRealPath());
        if (is_string($rows)){
            return $this->response()->error('文件读取异常：'.$rows);
        }
        log_trace_millisecond($hash,$start,var_export($rows,true));
        $n = 0;
        $errors = [];
        try {
            foreach ($collection as $model) {
                $server = $model->server;
                $config = $model->config;
                $ret = $this->storeProducts($server->ip, $config->db_user, $config->db_pass, $config->db_name, $rows);
                if ($ret['code']!=200){
                    $errors[] = sprintf('[#%s]%s:%s',$model->id,$model->domain,$ret['msg']);
                    continue;
                }
                $n++;
            }
        } catch (\Exception $e) {
            return $this->response()->error('导入失败：'.$e->getMessage());
        }
        if ($n) {
            return $this->response()->success(action_msg($this->name,$n,$errors))->refresh();
        }
        return $this->response()->error(action_msg($this->name,$n,$errors));
    }

    protected function readFile($realPath)
    {
        try {
            \Maatwebsite\Excel\Facades\Excel::load($realPath, function ($reader) use (&$data) {
                //获取excel的第几张表
                $reader = $reader->getSheet(0);
                //获取表中的数据
                $data = $reader->toArray();
            });
            if (!is_array($data) || !$data || !isset($data[0])) {
                return '文件异常.';
            }
            $columns = array_shift($data);
            $rows = [];
            foreach ($data as $row) {
                $tmp = [];
                foreach ($row as $index => $value) {
                    if (!isset($columns[$index])) {
                        continue;
                    }
                    $tmp[$columns[$index]] = trim_all($value);
                }
                $rows[] = $tmp;
            }
            return $rows;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    private function storeProducts($host, $db_user, $db_pass, $db_name, $records)
    {
        if (!is_array($records) || !$records) {
            return msg_error('参数不合法');
        }
        $importer = new ImportProduct($host, $db_user, $db_pass, $db_name);
        return $importer->storeProducts($records);
    }

}