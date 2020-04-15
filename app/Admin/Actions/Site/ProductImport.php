<?php

namespace App\Admin\Actions\Site;

use App\Admin\Extensions\Actions\XFormBatchAction;
use App\Imports\Admin\ProductsExcelImport;
use App\Libs\Site\ZenCart\ImportProduct;
use Carbon\Carbon;
use Encore\Admin\Actions\BatchAction;
use Encore\Admin\Facades\Admin;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ProductImport extends XFormBatchAction
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
        if (!$request->hasFile('product')) {
            return $this->response()->error('请上传文件');
        }
        $file = $request->file('product');
        $extension = $file->getClientOriginalExtension(); //获取上传图片的后缀名
        if (!in_array(strtolower($extension), ['xls', 'xlsx', 'csv'])) {
            return $this->response()->error('excel格式只允许csv,xls或者xlsx.');
        }
        $rows = $this->loadExcel($file);
        if (is_string($rows) || (is_array($rows) && !$rows)) {
            return $this->response()->error('文件内容读取异常：' . $rows);
        }
        $n = 0;
        $errors = [];
        $affected = [];
        try {
            foreach ($collection as $model) {
                $server = $model->server;
                $config = $model->config;
                $ret = $this->storeProducts($server->ip, $config->db_user, $config->db_pass, $config->db_name, $rows);
                if ($ret['code'] != 200) {
                    $errors[] = sprintf('[#%s]%s:%s', $model->id, $model->domain, $ret['msg']);
                    continue;
                }
                $n++;
                $affected[] = sprintf('%s:%s',$model->domain,$ret['msg']);
            }
        } catch (\Exception $e) {
            return $this->response()->error('导入失败：' . $e->getMessage());
        }

        if ($n) {
            return $this->response()->swal()
                ->success(action_msg($this->name, $n, $errors).'<br>'.implode('<br>',$affected))
                ->refresh();
        }
        return $this->response()->error(action_msg($this->name, $n, $errors));
    }

    protected function loadExcel($file)
    {
        try {
            $data = Excel::toArray((new ProductsExcelImport), $file);
            $columns = array_shift($data[0]);
            $rows = [];
            foreach ($data[0] as $row) {
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
            return sprintf('[%s:%s] %s',$e->getFile(),$e->getLine(),$e->getMessage());
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