<?php


namespace App\Admin\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GitToolController extends Controller
{
    protected $git_user;
    protected $git_email;

    public function __construct()
    {
        $this->git_email = getenv('GIT_USER_EMAIL');
        $this->git_user = getenv('GIT_USER_NAME');
        set_time_limit(0);
    }

    public function index(Request $request)
    {
        echo "<div style='margin-top:30px'><pre>";
        $acts = $request->input('acts') ? $request->input('acts') : 'os_disk';
        switch ($acts) {
            case "os_disk":
                echo "<hr>"
                    . "<b>" . shell_exec('whoami') . '</b>'
                    . shell_exec('df -h') . "<hr>";
                break;
            case "os_top":
                echo "<hr>", shell_exec('/usr/bin/top -bcn 1'), "<hr>";
                break;
            case "php-m":
                echo "<hr>", shell_exec('php -m'), "<hr>";
                break;
            case "phpinfo123":
                echo "<hr>", phpinfo(), "<hr>";
                break;
            case "git_pull":
                $dir = dirname(dirname(dirname(dirname(__FILE__))));
                $this->setGitGlobal();
                shell_exec('cd ' . $dir);
                echo "<hr>", date('Y-m-d H:i:s'), '<br>', $dir, '<br>'
                , shell_exec('/usr/bin/git pull 2>&1'), "<hr>";
                //echo shell_exec('cd ../'), shell_exec('/usr/bin/php artisan swoole:http restart 2>&1');
                break;
//            case "re_swoole":
//                $dir = dirname(dirname(dirname(dirname(__FILE__))));
//                $this->setGitGlobal();
//                shell_exec('cd ' . $dir);
//                echo "<hr>", date('Y-m-d H:i:s'), '<br>', $dir, '<br>'
//                , shell_exec('pwd;cd ../;/usr/bin/php artisan swoole:http restart 2>&1');
//                break;
            case "git_status":
                $dir = dirname(dirname(dirname(dirname(__FILE__))));
                $this->setGitGlobal();
                shell_exec('cd ' . $dir);
                echo "<hr>", date('Y-m-d H:i:s'), '<br>', $dir, '<br>'
                , shell_exec('/usr/bin/git status 2>&1'), "<hr>";
                break;
            case "git_merge_develop":
                $dir = dirname(dirname(dirname(dirname(__FILE__))));
                $this->setGitGlobal();
                shell_exec('cd ' . $dir);
                echo "<hr>", date('Y-m-d H:i:s'), '<br>', $dir, '<br>'
                , shell_exec('/usr/bin/git pull &&  git merge origin/master 2>&1')
                , "<hr>";
                break;
            case "git_reset_develop":
                $dir = dirname(dirname(dirname(dirname(__FILE__))));
                $this->setGitGlobal();
                shell_exec('cd ' . $dir);
                echo "<hr>", date('Y-m-d H:i:s'), '<br>', $dir, '<br>'
                    //,shell_exec('git pull origin develop:develop  && git reset --hard && git pull 2>&1')
                , shell_exec('git reset --hard origin/develop && git pull 2>&1')
                , "<hr>";
                break;
            case "git_stash_develop":
                $dir = dirname(dirname(dirname(dirname(__FILE__))));
                $this->setGitGlobal();
                shell_exec('cd ' . $dir);
                echo "<hr>", date('Y-m-d H:i:s'), '<br>', $dir, '<br>'
                , shell_exec('git stash && git pull && git stash pop && git pull 2>&1')
                , "<hr>";
                break;
            case "git_remove_develop":
                $dir = dirname(dirname(dirname(dirname(__FILE__))));
                $this->setGitGlobal();
                shell_exec('cd ' . $dir);
                echo "<hr>", date('Y-m-d H:i:s'), '<br>', $dir, '<br>'
                , shell_exec('git checkout master && git branch -D develop && git checkout develop && git pull 2>&1')
                , "<hr>";
                break;
            default:
                echo 'index';
                break;
        }
        echo "</pre></div>";
        return view('tools');
    }

    private function setGitGlobal()
    {
        shell_exec(sprintf('git config --global user.email "%s"', $this->git_email));
        shell_exec(sprintf('git config --global user.name "%s"', $this->git_user));
    }
}