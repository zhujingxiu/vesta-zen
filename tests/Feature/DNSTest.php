<?php


namespace Tests;


use App\Libs\CloudFlare\DNS;

class DNSTest extends TestCase
{
    public function testCloudFlareDNS()
    {
        $auth_key = config('cloudflare.auth_key');
        $auth_email = config('cloudflare.auth_email');
        $cf_dns = DNS::getInstance($auth_key, $auth_email);
        $domain = 'homeuom.com';
        $zone_id = $cf_dns->getZoneId($domain);
        $result = $cf_dns->records($zone_id);
        if ($result['code'] != 200){
            dd('records-error',$result);
        }
        $records = $result['data'];
        echo '<pre>';
        print_r($records);
        echo '</pre>';
        $ret = $cf_dns->getRecordByName($zone_id,'club1.'.$domain,$records);
        if ($ret['code'] != 200){
            dd('getRecordByName-error',$ret);
        }
        $record = $ret['data'];
        $ret = $cf_dns->updateRecord($zone_id,$record['id'],"ccclub",$record['content']);
        if ($ret['code'] != 200) {
            dd('updateRecord-error',$ret);
        }
        $ret = $cf_dns->record($zone_id,$record['id']);
        if ($ret['code'] != 200) {
            dd('record-error',$ret);
        }
        $ret = $cf_dns->getRecordByName($zone_id,'ttt.'.$domain,$records);
        if ($ret['code'] != 200){
            dd('getRecordByName-error',$ret);
        }
        $record = $ret['data'];
        $ret = $cf_dns->deleteRecord($zone_id,$record['id']);
        dd($records,$ret);
    }
}