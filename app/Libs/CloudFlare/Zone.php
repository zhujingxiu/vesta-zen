<?php


namespace App\Libs\CloudFlare;


class Zone extends Base
{

    public function zones()
    {
        $api_url = sprintf("%s/zones", $this->base_url);
        return $this->request($api_url);

    }


    public function addZone($name,$account_id,$jump_start=true,$type='full')
    {
        $api_url = sprintf("%s/zones",$this->base_url);
        $account = ['id'=>$account_id];
        return $this->request($api_url,compact('name','account','jump_start','type'),'POST');
    }
}