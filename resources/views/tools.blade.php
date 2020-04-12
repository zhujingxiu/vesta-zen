<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hengze-OA-Admin</title>
    <style>
        body{
            padding:24px;
            font-family:"Helvetica Neue", Helvetica, Arial, sans-serif;
            font-size: 18px;
            background: #111;
            color:#fff;
        }
        a{
            text-decoration: none;
            color:#fff;
        }
        a:link {
            color: #009;
            text-decoration: none;
            background-color:transparent;
        }
        .header{
            width:100%;
            position: absolute;
            top:0;
            left:0;
            background: #ffa500;
            padding:10px;
            padding-left:30px;
        }
    </style>
</head>
<body>
<div class="container">
   <div class="header">
        <a href="?acts=os_disk"> DISK </a> |
        <a href="?acts=os_top"> TOP </a> |
        <a href="?acts=php-m"> PHP </a> |
        <a href="?acts=git_pull"> Git-pull </a> |
       <a href="?acts=git_status"> Git-status </a> |

   </div>

    <p style="display: none">
        cd /app_dir && git pull
        <br>
        git reset --hard && git pull
        <br>
        git stash && git pull && git stash pop
    </p>

</div>
</body>
</html>