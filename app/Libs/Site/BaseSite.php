<?php


namespace App\Libs\Site;


use App\Libs\Vesta;

class BaseSite
{
    protected $vesta;
    protected $hash;
    protected $server_ip;
    protected $server_user;
    protected $server_pass;
    const ERR_ACCOUNT = 101;
    const ERR_DOMAIN = 102;

    const ERR_OK = 200;
    const ERR_SERVER = 202;

    const ERR_SITE_TPL = 301;
    const ERR_SITE_ADMIN = 302;
    const ERR_SITE_SRC = 303;
    const ERR_SITE_SQL = 304;
    const ERR_SITE_LANG = 305;
    const ERR_SITE_LANG_UP = 306;
    const ERR_SITE_CONF = 307;
    const ERR_SITE_CONF_WR = 308;
    const ERR_SITE_ZIP = 309;
    const ERR_SITE_ZIP_FTP = 310;
    const ERR_SITE_RESTORE = 311;
    const ERR_SITE_RESTORE_SQL = 312;

    const ERR_VESTA_WEB = 401;
    const ERR_VESTA_DB = 402;
    const ERR_VESTA_DOMAIN_DEL = 403;
    const ERR_VESTA_DB_DEL = 404;
    public function __construct($host, $user, $pass,$hash=null)
    {
        $this->vesta = new Vesta($host, $user, $pass);

        if ($this->checkAccount($user, $pass) != 0) {
            throw new \Exception(sprintf("Vesta账户信息[%s@%s]验证失败", $user, $host));
        };
        $this->server_ip = $host;
        $this->server_user = $user;
        $this->server_pass = $pass;
        $this->hash = $hash ?? str_random(16) . '==';
    }

    /**
     * 校验用户名密码是否有效
     * @param $user
     * @param $pass
     * @return array|bool|string
     */
    public function checkAccount($user, $pass)
    {
        return $this->vesta->checkUser($user, $pass);
    }

    protected function wrapperError($code, $errors)
    {
        return $this->errMsg($code) . "[ " . implode(" - ", $errors) . " ]";
    }

    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * @param $code
     * @return string
     */
    public function errMsg($code)
    {
        $msg = '';
        switch ($code) {
            case self::ERR_ACCOUNT:
                $msg = '提供的账户密码错误';
                break;
            case self::ERR_DOMAIN:
                $msg = '域名格式错误';
                break;

            case self::ERR_SITE_TPL:
                $msg = '网站模板目录异常';
                break;
            case self::ERR_SITE_ADMIN:
                $msg = '网站模板后台目录异常';
                break;
            case self::ERR_SITE_SRC:
                $msg = '网站模板副本异常';
                break;
            case self::ERR_SITE_SQL:
                $msg = '网站模板副本数据库文件异常';
                break;
            case self::ERR_SITE_LANG:
                $msg = '网站模板副本语言包目录异常';
                break;
            case self::ERR_SITE_LANG_UP:
                $msg = '网站模板语言配置更新异常';
                break;
            case self::ERR_SITE_CONF:
                $msg = '网站模板副本配置文件异常';
                break;
            case self::ERR_SITE_CONF_WR:
                $msg = '网站模板副本配置文件写入异常';
                break;
            case self::ERR_SITE_ZIP:
                $msg = '网站模板副本zip压缩写入异常';
                break;
            case self::ERR_SITE_ZIP_FTP:
                $msg = '网站模板副本zip压缩文件传输异常';
                break;
            case self::ERR_SITE_RESTORE:
                $msg = '网站模板目录恢复异常';
                break;
            case self::ERR_SITE_RESTORE_SQL:
                $msg = '网站模板目录数据库恢复异常';
                break;
            case self::ERR_VESTA_WEB:
                $msg = 'Vesta添加Web出错';
                break;
            case self::ERR_VESTA_DB:
                $msg = 'Vesta添加数据库出错';
                break;
            case self::ERR_VESTA_DOMAIN_DEL:
                $msg = 'Vesta删除域名出错';
                break;
            case self::ERR_VESTA_DB_DEL:
                $msg = 'Vesta删除数据库出错';
                break;
            default:
                $msg = '未知错误';
                break;
        }
        return $msg;
    }

    /**
     * @param array $data
     * @param string $msg
     * @return array
     */
    protected function success($data = [], $msg = '')
    {
        return $this->return_msg(self::ERR_OK, $msg, $data);
    }

    /**
     * @param string $msg
     * @param null $return_code
     * @return array
     */
    protected function error($msg = '', $return_code = null)
    {
        if (is_numeric($msg)) {
            $return_code = $msg;
            $message = $this->errMsg($return_code);
        } else {
            $message = $msg ?? '未知错误';
            $return_code = $return_code ?? self::ERR_SERVER;
        }
        return $this->return_msg($return_code, $message, []);
    }

    /**
     * @param $return_code
     * @param string $msg
     * @param array $data
     * @return array
     */
    protected function return_msg($return_code, $msg = '', $data = [])
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
            'data' => $data
        ];
    }
}