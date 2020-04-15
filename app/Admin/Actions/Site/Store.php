<?php

namespace App\Admin\Actions\Site;


use App\Admin\Extensions\Actions\XFormBatchAction;
use App\Libs\Site\ZenCart\ZenCart;
use App\Models\ZenCartCountries;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class Store extends XFormBatchAction
{
    public $name = '设置商店信息';
    protected $selector = '.site-config';


    public function html()
    {
        return <<<HTML
        <a class="btn btn-sm btn-default site-config">设置商店信息</a>
HTML;
    }

    public function xForm($form)
    {
        $countries = ZenCartCountries::status(1)
            ->selectRaw('countries_id as id,CONCAT(countries_id," ",countries_name) AS t')
            ->pluck('t','id');
        $form->text('store_name', '商店标题');
        $form->text('store_owner', '商店店主');
        $form->text('store_email', 'Email地址');
        $form->loadSelect('store_country', '所在国家')->options($countries)->load('store_zone','api/zones');
        $form->select('store_zone','所属区域');
        $form->textarea('store_address', '详细店址');
        return $form;
    }


    public function handle(Collection $collection, Request $request)
    {
        $n = 0;
        $errors = [];
        $store_name = $request->get('store_name');
        $store_owner = $request->get('store_owner');
        $store_email = $request->get('store_email');
        $store_country = $request->get('store_country');
        $store_zone = $request->get('store_zone');
        $store_address = $request->get('store_address');

        foreach ($collection as $model) {
            $config = $model->config;
            $server = $model->server;
            $zen = new ZenCart($server->ip,$config->db_user,$config->db_pass,$config->db_name);
            $ret1 = $zen->config($store_name,'STORE_NAME');
            $ret2 = $zen->config($store_owner,'STORE_OWNER');
            $ret3 = $zen->config($store_email,'STORE_OWNER_EMAIL_ADDRESS','EMAIL_FROM', 'SEND_EXTRA_ORDER_EMAILS_TO',
                'SEND_EXTRA_CREATE_ACCOUNT_EMAILS_TO', 'SEND_EXTRA_LOW_STOCK_EMAILS_TO',
                'SEND_EXTRA_GV_CUSTOMER_EMAILS_TO', 'SEND_EXTRA_GV_ADMIN_EMAILS_TO',
                'SEND_EXTRA_DISCOUNT_COUPON_ADMIN_EMAILS_TO',
                'SEND_EXTRA_ORDERS_STATUS_ADMIN_EMAILS_TO',
                'SEND_EXTRA_REVIEW_NOTIFICATION_EMAILS_TO', 'MODULE_PAYMENT_CC_EMAIL');
            $ret4 = $zen->config($store_country,'STORE_COUNTRY', 'SHIPPING_ORIGIN_COUNTRY');
            $ret5 = $zen->config($store_zone,'STORE_ZONE');
            $ret6 = $zen->config($store_address,'STORE_NAME_ADDRESS');
            if (!$ret1 || !$ret2 || !$ret3 || !$ret4|| !$ret5|| !$ret6){
                $errors[] = sprintf("[#%s]%s",$model->id,$model->domain);
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