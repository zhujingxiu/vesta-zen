<?php

namespace App\Libs;

class Vesta
{
    protected $host;
    protected $user;
    protected $pwd;

    public function __construct($hostname, $user, $pwd)
    {
        $this->host = $hostname;
        $this->user = $user;
        $this->pwd = $pwd;
    }

    public function addUser($username, $password, $email = "", $package = "", $fist_name = "", $last_name = "")
    {
        return $this->run("v-add-user", [
            'arg1' => $username,
            'arg2' => $password,
            'arg3' => $email,
            'arg4' => $package,
            'arg5' => $fist_name,
            'arg6' => $last_name,
        ]);
    }

    public function addDomain($username, $domain)
    {
        return $this->run("v-add-domain", [
            'arg1' => $username,
            'arg2' => $domain,
        ]);
    }

    public function addDatabase($username, $db_name, $db_user, $db_pass)
    {

        return $this->run("v-add-database", [
            'arg1' => $username,
            'arg2' => $db_name,
            'arg3' => $db_user,
            'arg4' => $db_pass,
        ]);
    }

    public function deleteDomain($username, $domain)
    {
        return $this->run("v-delete-domain", [
            'arg1' => $username,
            'arg2' => $domain,
        ]);
    }

    public function deleteUser($username)
    {
        return $this->run("v-delete-user", [
            'arg1' => $username,
        ]);
    }

    public function deleteDatabase($username, $database)
    {
        return $this->run("v-delete-database", [
            'arg1' => $username,
            'arg2' => $database,
        ]);
    }

    public function addDomainFTPUser($username, $domain, $ftp_user, $ftp_pwd, $ftp_path = "")
    {
        return $this->run("v-add-web-domain-ftp", [
            'arg1' => $username,
            'arg2' => $domain,
            'arg3' => $ftp_user,
            'arg4' => $ftp_pwd,
            'arg5' => $ftp_path,
        ]);
    }

    public function changeDomainFTPPwd($username, $domain, $ftp_user, $ftp_pwd)
    {
        return $this->run("v-change-web-domain-ftp-password", [
            'arg1' => $username,
            'arg2' => $domain,
            'arg3' => $ftp_user,
            'arg4' => $ftp_pwd,
        ]);
    }

    public function changeDomainFTPPath($username, $domain, $ftp_user, $ftp_path)
    {
        return $this->run("v-change-web-domain-ftp-path", [
            'arg1' => $username,
            'arg2' => $domain,
            'arg3' => $ftp_user,
            'arg4' => $ftp_path,
        ]);
    }

    public function checkCPU()
    {
        return $this->run("v-list-sys-cpu-status", [], false);
    }

    public function checkMemory()
    {
        return $this->run("v-list-sys-memory-status", [], false);
    }

    public function listUserPackages($format = "json")
    {
        $format = trim_all(strtolower($format));
        $ret = $this->run("v-list-user-packages", [
            'arg1' => $format,
        ], false);

        return $format == 'json' ? json_decode($ret, true) : $ret;
    }

    public function listUserPackage($package, $format = "json")
    {
        $format = trim_all(strtolower($format));
        $ret = $this->run("v-list-user-package", [
            'arg1' => $package,
            'arg2' => $format,
        ], false);

        return $format == 'json' ? json_decode($ret, true) : $ret;
    }

    /**
     * WEB_DOMAINS='1000'
     * WEB_ALIASES='1000'
     * DATABASES='1000'
     * @param $root_pass
     * @param $username
     * @param $web_domains
     * @param $web_alias
     * @param $databases
     * @return array|bool|string
     */
    public function changeAdminPackageConfig($root_pass,$username)
    {
        $admin_pkg = "./data/admin.pkg";
        if (!file_exists($admin_pkg)){
            return ['E_PKG','admin package file not exists'];
        }
        try {
            $connection = ssh2_connect($this->host, 22);
            ssh2_auth_password($connection, 'root', $root_pass);
            if(ssh2_scp_send($connection, $admin_pkg,"/usr/local/vesta/data/packages/admin.pkg",0644)){
                return $this->run("v-change-user-package", [
                    'arg1' => $username,
                    'arg2' => "admin",
                    'arg3' => "yes",
                ]);
            }else{
                return ['E_CHANGE_PKG',"admin.pkg文件传输失败"];
            }

        } catch (\Exception $e) {
            return ['E_CHANGE_PKG',$e->getMessage()];
        }
    }

    /**
     * v-update-user-package
     * update user package
     * OPTIONS:
     * PACKAGE
     * The function propagates package to connected users
     */
    public function updateUserPackage($package, $format = 'json')
    {
        return $this->run("v-update-user-package", [
            'arg1' => $package,
            'arg2' => $format
        ], false);
    }

    /**
     * v-add-user-package
     * add user package
     * OPTIONS:
     * PKG_DIR PACKAGE [REWRITE]
     * The function adds new user package to the system.
     */
    public function addUserPackage($pkg_dir, $package, $rewrite = 'yes')
    {
        return $this->run("v-add-user-package", [
            'arg1' => $pkg_dir,
            'arg2' => $package,
            'arg3' => $rewrite
        ], false);
    }

    public function checkUser($username, $password)
    {
        return $this->run("v-check-user-password", [
            'arg1' => $username,
            'arg2' => $password,
        ]);
    }

    public function listUsers($username, $format = "json")
    {
        $format = trim_all(strtolower($format));
        $ret = $this->run("v-list-user", [
            'arg1' => $username,
            'arg2' => $format,
        ], false);

        return $format == 'json' ? json_decode($ret, true) : $ret;
    }

    public function listDomains($username, $domain, $format = "json")
    {
        $format = trim_all(strtolower($format));
        $ret = $this->run("v-list-web-domain", [
            'arg1' => $username,
            'arg2' => $domain,
            'arg3' => $format,
        ], false);

        return $format == 'json' ? json_decode($ret, true) : $ret;
    }

    /**
     * @param $username
     * @param string $format
     * @return array|bool|mixed|string
     */
    public function listDatabases($username, $format = "json")
    {
        $format = trim_all(strtolower($format));
        $ret = $this->run("v-list-databases", [
            'arg1' => $username,
            'arg2' => $format,
        ], false);
        return $format == 'json' ? json_decode($ret, true) : $ret;
    }

    public function listDatabaseHosts($username, $format = "json")
    {
        $format = trim_all(strtolower($format));
        $ret = $this->run("v-list-database-hosts", [
            'arg1' => $username,
            'arg2' => $format,
        ], false);
        return $format == 'json' ? json_decode($ret, true) : $ret;
    }

    protected function run($command, $data, $returncode = true)
    {
        $requestData = $this->formatRequestData($command, $data, $returncode);

        return $this->curlRequest($requestData, $returncode);
    }

    protected function formatRequestData($command, $postdata, $returncode = true)
    {
        $data = [
            'user' => $this->user,
            'password' => $this->pwd,
            'cmd' => $command,
        ];
        if ($returncode) {
            $data['returncode'] = "yes";
        }
        $data = array_merge($data, $postdata);
        return http_build_query($data);
    }

    protected function curlRequest($postdata, $returncode = true)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://' . $this->host . ':8083/api/');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
        $returnCode = curl_exec($curl);
        //echo "curlRequest-returnCode:".var_dump($returnCode);
        return $returncode ? $this->formatReturnCode($returnCode) : $returnCode;
    }

    protected function formatReturnCode($code)
    {
        if (!is_numeric($code)) {
            return ['E_UNKNOWN', 'false or null'];
        }

        switch ($code) {
            case 1:
                return ['E_ARGS', 'Not enough arguments provided'];
            case 2:
                return ['E_INVALID', 'Object or argument is not valid'];
            case 3:
                return ['E_NOTEXIST', 'Object doesn\'t exist'];
            case 4:
                return ['E_EXISTS', 'Object already exists'];
            case 5:
                return ['E_SUSPENDED', 'Object is suspended'];
            case 6:
                return ['E_UNSUSPENDED', 'Object is already unsuspended'];
            case 7:
                return ['E_INUSE', 'Object can\'t be deleted because is used by the other object'];
            case 8:
                return ['E_LIMIT', 'Object cannot be created because of hosting package limits'];
            case 9:
                return ['E_PASSWORD', 'Wrong password'];
            case 10:
                return ['E_FORBIDEN', 'Object cannot be accessed be the user'];
            case 11:
                return ['E_DISABLED', 'Subsystem is disabled'];
            case 12:
                return ['E_PARSING', 'Configuration is broken'];
            case 13:
                return ['E_DISK', 'Not enough disk space to complete the action'];
            case 14:
                return ['E_LA', 'Server is to busy to complete the action'];
            case 15:
                return ['E_CONNECT', 'Connection failed. Host is unreachable'];
            case 16:
                return ['E_FTP', 'FTP server is not responding'];
            case 17:
                return ['E_DB', 'Database server is not responding'];
            case 18:
                return ['E_RRD', 'RRDtool failed to update the database'];
            case 19:
                return ['E_UPDATE', 'Update operation failed'];
            case 20:
                return ['E_RESTART', 'Service restart failed'];
            default:
                return 'OK';
        }
    }

    public function __toString()
    {
        return sprintf("host:%s|user:%s|pass:%s", $this->host, $this->user, $this->pwd);
    }


}