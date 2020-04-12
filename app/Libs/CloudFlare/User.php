<?php


namespace App\Libs\CloudFlare;


class User extends Base
{
    public function profile()
    {
        $api_url = sprintf("%s/user", $this->base_url);

        return $this->request($api_url, []);
    }
    public function accounts($page=1,$per_page=20,$direction="desc")
    {
        $api_url = sprintf("%s/accounts", $this->base_url);

        return $this->request($api_url, compact('page','per_page','direction'));
    }

    public function getFirstAccount()
    {
        $result = $this->accounts();
        if ($result['code']!=200) {
            return $this->error($result['msg']);
        }
        $account = current($result['data']);
        return $this->success($account);
    }
}