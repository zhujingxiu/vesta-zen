<?php

namespace App\Admin\Actions\Site;

use App\Admin\Extensions\Actions\XFormBatchAction;
use App\Libs\Site\ZenCart\ZenCart;
use App\Models\ZenCartCurrencies;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class Currency extends XFormBatchAction
{
    public $name = '更新货币设置';
    protected $selector = '.site-currency';

    public function html()
    {
        return <<<HTML
        <a class="btn btn-sm btn-default site-currency">更新货币设置</a>
HTML;
    }
    public function form()
    {
        $currencies = ZenCartCurrencies::selectRaw('CONCAT(currencies_id," ",title) AS t,code')->pluck('t','code');
        $this->select('currency', '默认货币')
            ->options($currencies);
    }
    public function handle(Collection $collection, Request $request)
    {
        $currency = $request->get('currency');
        $n = 0;
        $errors = [];
        foreach ($collection as $model) {
            $config = $model->config;
            $server = $model->server;

            $ret = app(ZenCart::class,[
                'host'=>$server->ip,
                'db_user'=>$config->db_user,
                'db_pass'=>$config->db_pass,
                'db_name'=>$config->db_name,
            ])->config($currency,'DEFAULT_CURRENCY');
            if ($ret['code']){
                $errors[] = sprintf('[#%s]%s:%s',$model->id,$model->domain,$ret['msg']);
                continue;
            }
            $n++;
        }
        if ($n) {
            return $this->response()->success(action_msg($this->name,$n,$errors))->refresh();
        }
        return $this->response()->error(action_msg($this->name,$n,$errors));
    }


}