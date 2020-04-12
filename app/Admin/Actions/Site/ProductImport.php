<?php

namespace App\Admin\Actions\Site;

use App\Libs\Site\ZenCart\ImportProduct;
use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

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
        if ($request->hasFile('product')) {
            $file = $request->file('product');
            $extension = $file->getClientOriginalExtension(); //获取上传图片的后缀名
            if (!in_array(strtolower($extension), ['xls', 'xlsx', 'csv'])) {
                return ['code' => 202, 'data' => null, 'msg' => 'excel格式只允许csv,xls或者xlsx'];
            }
            $realPath = $file->getRealPath();   //临时文件的绝对路径
            $data = '';
            \Maatwebsite\Excel\Facades\Excel::load($realPath, function ($reader) use (&$data) {
                //获取excel的第几张表
                $reader = $reader->getSheet(0);
                //获取表中的数据
                $data = $reader->toArray();
            });
            if (!is_array($data) || !$data || !isset($data[0])) {
                return $this->response()->error('文件异常.');
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
            $n = 0;
            $errors = [];
            try {
                foreach ($collection as $model) {
                    $server = $model->server;
                    $config = $model->config;
                    $ret = $this->storeProducts($server->ip, $config->db_user, $config->db_pwd, $config->db_name, $rows);
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
                return $this->response()->success(sprintf('产品导入成功：%s个站点成功，错误信息：%s', $n, implode("<br>", $errors)))->refresh();
            }
            return $this->response()->error(sprintf('产品导入站点失败：%s', implode('<br>', $errors)));
        }
        return $this->response()->error('请上传文件');
    }

    private function storeProducts($host, $db_user, $db_pass, $db_name, $records)
    {
        if (!is_array($records) || !$records) {
            return false;
        }
        $importer = new ImportProduct($host, $db_user, $db_pass, $db_name);
        return $importer->storeProducts($records);
    }

}