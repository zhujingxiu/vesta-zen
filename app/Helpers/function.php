<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use \Symfony\Component\HttpFoundation\File\UploadedFile;
if (!function_exists('upload_image')) {

    /**
     * 图片上传
     * @param UploadedFile $file
     * @param string $disk
     * @param string $prefix
     * @return array
     */
    function upload_image(UploadedFile $file, $disk = 'public', $prefix = '')
    {
            if (!$file->isValid()) {
                return msg_error('上传的文件不可用');
            }
            $ext = $file->getClientOriginalExtension();//获取上传图片的后缀名
            if (!in_array(strtolower($ext), ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'bmp', 'txt', 'doc', 'csv', 'xls', 'xlsx', 'ini'])) {
                return msg_error('文件格式只允许pdf,jpg,jpeg,png,gif,bmp,txt,doc,csv,xls,xlsx,ini');
            }
            $date = date('ymd');
            $prefix_dir = (($prefix) ? trim($prefix, '/') . '/' : '') . $date;

            $fileName = sprintf("%s/%s.%s", $prefix_dir, date('His') . '-' . str_random(9), $ext);
            if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png'])) {
                $dest_path = \Storage::disk($disk)->path('');
                $serve = app(\App\Services\GDServices::class, ['path' => $file->getRealPath()]);
                if (!is_dir($dest_path)) {
                    mkdir($dest_path, 0755, true);
                }
                $bool = $serve->compressSize()->generate($dest_path);
            } else {
                $realPath = $file->getRealPath();   //临时文件的绝对路径
                $bool = Storage::disk($disk)->put($fileName, file_get_contents($realPath));
            }
//            $realPath = $file->getRealPath();   //临时文件的绝对路径
//            $bool = Storage::disk($disk)->put($fileName, file_get_contents($realPath));
            if ($bool) {
                return msg_success('图片上传成功', ['path' => '/' . $fileName]);
            }
            return msg_error('图片上传失败');


    }
}
if (!function_exists('new_db_connection')) {
    function new_db_connection($server_ip, $db_user, $db_pass, $db_name)
    {
        $databases = app()['config']['database'];
        $connection = md5(uniqid($db_name . '-' . $server_ip));
        $databases['connections'][$connection]['driver'] = 'mysql';
        $databases['connections'][$connection]['host'] = $server_ip;
        $databases['connections'][$connection]['username'] = $db_user;
        $databases['connections'][$connection]['password'] = $db_pass;
        $databases['connections'][$connection]['database'] = $db_name;
        $databases['connections'][$connection]['charset'] = "utf8";
        app()['config']['database'] = $databases;
        return $connection;
    }
}

if (!function_exists('check_table_column')) {
    function check_table_column($conn, $table_name, $column_name,$param = null)
    {
        try {
            $sql = "SHOW COLUMNS FROM " . $table_name;
            $result = \DB::connection($conn)->select($sql);
            $result = array_map("get_object_vars", $result);
            foreach ($result as $row) {
                if ($row['Field'] != $column_name) {
                    continue;
                }
                $param = $param ? strtolower(trim_all($param)) : '';
                $max_length = preg_replace('/[a-z\(\)]/', '', $row['Type']);
                if ($param == 'length'){
                    return $max_length;
                }
                preg_match('/^[a-z]*/', $row['Type'], $matches);
                if ($param == 'type'){
                    return $matches[0];
                }
                if ($param == 'key'){
                    return $row['Key'];
                }
                return [
                    "field" => $row['Field'],
                    "type" => strtolower($matches[0]),
                    "null" => strtolower($row['Null']),
                    "key" => strtolower($row['Key']),
                    "default" => $row['Default'],
                    "extra" => strtolower($row['Extra']),
                    "length" => $max_length
                ];
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}

if (!function_exists('site_db_info')) {
    function site_db_info()
    {
        return [
            config('site.default_user'),
            config('site.default_pass')
        ];
    }
}

if (!function_exists('log_trace_millisecond')) {
    function log_trace_millisecond($hash, $start_carbon, ...$args)
    {
        \Illuminate\Support\Facades\Log::info($hash . var_export([
                    'start' => $start_carbon,
                    'end' => Carbon::now()->format('Y-m-d H:i:s.u'),
                    'diff' => Carbon::now()->diffInMilliseconds($start_carbon),
                ] + $args, true));
    }
}


if (!function_exists('str_random')) {
    function str_random($length, $application_id = null)
    {
        $str = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet .= "0123456789";

        mt_srand($application_id ?? microtime());      // Call once. Good since $application_id is unique.

        for ($i = 0; $i < $length; $i++) {
            $str .= $codeAlphabet[mt_rand(0, strlen($codeAlphabet) - 1)];
        }
        return $str;
    }
}

if (!function_exists('trim_all')) {
    /**
     * 消灭所有不可见的字符
     * @param $str
     * @return mixed
     */
    function trim_all($str)
    {
        $qian = array(" ", "　", "\t", "\n", "\r", ",,");
        $hou = array("", "", "", "", "", "");
        return str_replace($qian, $hou, $str);
    }
}

if (!function_exists('update_batch')) {
    /**
     * 更新多条数据
     * @param $table_name
     * @param $con
     * @param array $records
     * @return bool|int
     */
    function update_batch($con, $table_name, $records = [])
    {
        if ($table_name && !empty($records)) {
            // column or fields to update
            $updateColumn = array_keys($records[0]);
            $referenceColumn = $updateColumn[0]; //e.g id
            unset($updateColumn[0]);
            $whereIn = "";
            $q = "UPDATE " . $table_name . " SET ";
            foreach ($updateColumn as $uColumn) {
                $q .= $uColumn . " = CASE ";

                foreach ($records as $data) {
                    $q .= "WHEN " . $referenceColumn . " = " . $data[$referenceColumn] . " THEN '" . $data[$uColumn] . "' ";
                }
                $q .= "ELSE " . $uColumn . " END, ";
            }
            foreach ($records as $data) {
                $whereIn .= "'" . $data[$referenceColumn] . "', ";
            }
            $q = rtrim($q, ", ") . " WHERE " . $referenceColumn . " IN (" . rtrim($whereIn, ', ') . ")";
            // Update
            return DB::connection($con)->update(DB::raw($q));
        } else {
            return false;
        }

    }
}

if (!function_exists('table_fields')) {
    /**
     * @param $table_name
     * @param $con
     * @return bool
     */
    function table_fields($table_name, $con)
    {
        if ($table_name && $con) {
            $result = DB::connection($con)->select('SHOW FULL COLUMNS from ' . $table_name);
            $result = array_map('get_object_vars', $result);
            foreach ($result as $value) {
                if (!$value['Comment']) {
                    $value['Comment'] = ucwords($value['Field']);
                }
                $arr[$value['Field']] = $value;
            }
            return $arr;
        } else {
            return false;
        }
    }
}

if (!function_exists('msg_success')) {
    /** api成功返回
     * @param $data
     * @param $msg
     * @return array
     */
    function msg_success($msg='', $data = null)
    {
        return ['code' => 200, 'data' => $data, 'msg' => $msg];
    }
}

if (!function_exists('msg_error')) {
    /** api失败返回
     * @param $msg
     * @param null $data
     * @param int $code
     * @return array
     */
    function msg_error($msg, $data = null, $code = 202)
    {
        return ['code' => $code, 'data' => $data, 'msg' => $msg];
    }
}

if (!function_exists('is_mobile')) {
    /**
     * 验证手机号是否正确
     * @param $mobile
     * @return bool
     */
    function is_mobile($mobile)
    {
        $mobile = trim_all($mobile);
        return preg_match('/^1\d{10}$/', $mobile) ? true : false;
    }
}

if (!function_exists('upload_prefix')) {

    function upload_prefix($file)
    {
        return asset('upload') . '/' . $file;
    }
}

if (!function_exists('key_to_value_where')) {
    /**
     * 返回某表的键值对
     * @param $conn
     * @param $table
     * @param $key
     * @param $value
     * @param array $where
     * @return array
     */
    function key_to_value_where($conn, $table, $key, $value, $where = [])
    {
        $arr = [];
        $cache_key = 'key_to_value_where' . md5($table . $key . $value . implode(',', $where));
        $cache_val = Cache::get($cache_key);
        if ($cache_val) {
            //从缓存读取
            $arr = $cache_val;
        } else {
            //从数据读取
            $objs = DB::connection($conn)->table($table)->select($key, $value)->where($where)->get();
            foreach ($objs as $obj) {
                $arr[$obj->$key] = $obj->$value;
            }
            if ($arr) {
                Cache::set([$cache_key => $arr], 10);
            }
        }
        return $arr;
    }
}

if (!function_exists('parse_domain')) {
    function parse_domain($httpurl)
    {
        $httpurl = strtolower(trim($httpurl));
        if (empty($httpurl)) return;
        $regx1 = '/(https?:\/\/)?(([^\/\?#&]+\.)?([^\/\?#&\.]+\.)(com\.cn|org\.cn|net\.cn|com\.jp|co\.jp|com\.kr|com\.tw)(\:[0-9]+)?)\/?/i';
        $regx2 = '/(https?:\/\/)?(([^\/\?#&]+\.)?([^\/\?#&\.]+\.)(app|cn|com|org|info|us|fr|de|tv|net|cc|biz|hk|jp|kr|name|me|tw|la)(\:[0-9]+)?)\/?/i';
        $host = $tophost = '';
        if (preg_match($regx1, $httpurl, $matches)) {
            $host = $matches[2];
        } elseif (preg_match($regx2, $httpurl, $matches)) {
            $host = $matches[2];
        }
        if ($matches) {
            $tophost = $matches[4] . $matches[5];
            $domainLevel = $matches[3] == 'www.' ? 1 : (substr_count($matches[3], '.') + 1);
        } else {
            $tophost = '';
            $domainLevel = 0;
        }
        return array($domainLevel, $tophost, $host);
    }

}

if (!function_exists('copy_folder')) {
    function copy_folder($dirSrc, $dirTo)
    {
        if (is_file($dirTo)) {
            return;
        }
        if (!file_exists($dirTo)) {
            mkdir($dirTo);
        }

        if ($handle = opendir($dirSrc)) {
            while ($filename = readdir($handle)) {
                if ($filename != '.' && $filename != '..') {
                    $subsrcfile = $dirSrc . '/' . $filename;
                    $subtofile = $dirTo . '/' . $filename;
                    if (is_dir($subsrcfile)) {
                        copy_folder($subsrcfile, $subtofile);//再次递归调用copydir
                    }
                    if (is_file($subsrcfile)) {
                        copy($subsrcfile, $subtofile);
                    }
                }
            }
            closedir($handle);
        }
    }
}

if (!function_exists('del_folder')) {
    function del_folder($dirname)
    {
        $result = false;
        if (!is_dir($dirname)) {
            return false;
        }
        $handle = opendir($dirname); //打开目录
        while (($file = readdir($handle)) !== false) {
            if ($file != '.' && $file != '..') {
                //排除"."和"."
                $dir = $dirname . '/' . $file;
                is_dir($dir) ? del_folder($dir) : unlink($dir);
            }
        }
        closedir($handle);
        $result = rmdir($dirname) ? true : false;
        return $result;
    }
}

if (!function_exists('is_ip')) {
    function is_ip($ip_str)
    {
        $str = trim($ip_str);
        if (preg_match('/\.0\d*/', $str, $array)) {
            return false;
        }
        if (ip2long($str) == -1) {
            return false;
        }
        return true;
    }
}