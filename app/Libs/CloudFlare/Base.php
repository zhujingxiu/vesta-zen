<?php


namespace App\Libs\CloudFlare;


use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Base
{
    protected $base_url = "https://api.cloudflare.com/client/v4";
    protected $auth_key;
    protected $auth_email;

    const ERR_OK = 200;
    const ERR_RESPONSE = 201;
    const ERR_SERVER = 202;
    const ERR_DNS_TYPE = 203;
    const ERR_DNS_NAME = 204;
    const ERR_DNS_CONTENT = 205;
    const ERR_DNS_RECORD = 206;

    public function __construct($auth_key, $auth_email)
    {
        $this->auth_key = $auth_key;
        $this->auth_email = $auth_email;
    }

    /**
     * cURL 请求API
     * @param $api_url
     * @param array $data
     * @param string $method
     * @return array
     */
    protected function request($api_url, $data = [], $method = "GET")
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $api_url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
                "X-Auth-Email: {$this->auth_email}",
                "X-Auth-Key: {$this->auth_key}"
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            if (is_array($data) && $data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            $r = curl_exec($ch);
            curl_close($ch);
            // $s = Carbon::now()->format('Hisu');
            // Log::info(sprintf('%s-DNS-request-%s:%s[%s]',$s,$method,$api_url,var_export($data,true)));
            // Log::info(sprintf('%s-DNS-response-%s:%s[%s]',$s,$method,$api_url,$r));
            return $this->response($r, $api_url);
        } catch (\Exception $e) {
            //dd($e->getMessage(),json_decode($e->getMessage(),true));
            $ret = $this->response($e->getMessage(), $api_url);
            return $ret;
        }
    }

    /**
     * 处理响应结果
     * @param $result
     * @return array
     */
    protected function response($result, $api_url)
    {
        $response = json_decode($result, true);
        if (!is_array($response)
            || !isset($response['success'])
            || !isset($response['errors'])
            || !isset($response['messages'])) {
            //dd('ERR_RESPONSE', $result, $response);
            return $this->error(self::ERR_RESPONSE);
        }

        if ($response['success']) {
            return $this->success($response['result'], $response['result_info'] ?? []);
        } else {
            if (isset($response['errors']['message'])) {
                $msg = $response['errors']['message'];
            } else {
                $msg = [];
                foreach ($response['errors'] as $item) {
                    $msg[] = implode(" - ", $item);
                }
                if ($msg)
                    $msg = var_export([implode(" ", $msg), $api_url], true);
            }
            return $this->error($msg);
        }
    }

    protected function success($data = [], $meta = [], $msg = '')
    {
        return $this->return_msg(self::ERR_OK, $msg, $data, $meta);
    }

    protected function error($msg = '')
    {
        if (is_numeric($msg)) {
            $return_code = $msg;
            $message = $this->errMsg($return_code);
        } else {
            $message = $msg ?? '未知错误';
            $return_code = self::ERR_SERVER;
        }
        return $this->return_msg($return_code, $message, [], []);
    }

    /**
     * @param $return_code
     * @param string $msg
     * @param array $data
     * @param array $meta
     * @return array
     */
    protected function return_msg($return_code, $msg = '', $data = [], $meta = [])
    {
        if ($return_code == self::ERR_OK) {
            $message = $msg ?? '';
        } else if ($msg) {
            $message = $msg;
        } else {
            $message = $this->errMsg($return_code);
        }
        return [
            'code' => $return_code,
            'msg' => $message,
            'data' => $data,
            'meta' => $meta
        ];
    }

    /**
     * @param $return_code
     * @return string
     */
    protected function errMsg($return_code)
    {
        $msg = '';
        switch ((int)$return_code) {
            case self::ERR_RESPONSE:
                $msg = '响应格式不合法';
                break;
            case self::ERR_DNS_TYPE:
                $msg = 'DNS记录Type参数不合法';
                break;
            case self::ERR_DNS_NAME:
                $msg = 'DNS记录Name参数不合法';
                break;
            case self::ERR_DNS_CONTENT:
                $msg = 'DNS记录Content参数不合法';
                break;
            case self::ERR_DNS_RECORD:
                $msg = 'DNS记录不存在或状态异常';
                break;
            default:
                $msg = '未知错误';
                break;
        }
        return $msg;
    }
}