<?php


namespace App\Libs\Site\ZenCart\Struct;


interface FilePath
{
    const DIR_WS_CATALOG =  '/';
    const DIR_WS_IMAGES =  'images/';
    const DIR_WS_INCLUDES =  'includes/';
    const DIR_WS_FUNCTIONS =  self::DIR_WS_INCLUDES . 'functions/';
    const DIR_WS_CLASSES =  self::DIR_WS_INCLUDES . 'classes/';
    const DIR_WS_MODULES =  self::DIR_WS_INCLUDES . 'modules/';
    const DIR_WS_LANGUAGES =  self::DIR_WS_INCLUDES . 'languages/';
    const DIR_WS_DOWNLOAD_PUBLIC =  self::DIR_WS_CATALOG . 'pub/';
    const DIR_WS_TEMPLATES =  self::DIR_WS_INCLUDES . 'templates/';
    const DIR_WS_UPLOADS =  self::DIR_WS_IMAGES . 'uploads/';
    const DIR_FS_UPLOADS =  self::DIR_WS_UPLOADS;
    const DIR_FS_EMAIL_TEMPLATES =  '__DIR_FS_CATALOG__email/';
    const DIR_FS_DOWNLOAD_PUBLIC =  '__DIR_FS_CATALOG__pub/';
    const DIR_FS_SQL_CACHE =  '__DIR_FS_CATALOG__cache';
    const DIR_FS_LOGS =  '__DIR_FS_CATALOG__logs';
    const DIR_FS_DOWNLOAD =  '__DIR_FS_CATALOG__download/';
}