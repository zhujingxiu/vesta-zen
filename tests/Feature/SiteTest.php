<?php


namespace Tests;

use App\Libs\Site\Site;
class SiteTest extends TestCase
{

    public function testAddSite()
    {
        $server_ip = "46.4.85.58";
        $server_user = "admin";
        $server_pwd = "tgJoyl5pru";
        $domain = strtolower(sprintf("%s.example.app",str_random(4)));
        $domain_level = 1;
        $lang = 'english';
        $tpl = "./sites/zen-cart-2020";
        try {
            $site = new Site($server_ip, $server_user, $server_pwd);//
            $ret = $site->run($domain, $domain_level, $tpl, $lang);
            dd($ret);
        } catch (\Exception $e) {
            dd($e);
        }
    }
}