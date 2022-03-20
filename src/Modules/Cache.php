<?php

namespace Letov\Flycatcher\Modules;

class Cache
{
    /*function isEmptyFile($file) {
        $cmd = "stat --printf=\"%s\" \"{$file}\"";
        $size = (int)shell_exec($cmd) ;
        return $size == 0;
    }*/

    static function valid($file) {
        if (!file_exists($file)) {
            return false;
        }
        /*if (!self::isEmptyFile($file)) {
            Cache::unlinkIfExist($filePath);
            return false;
        }
        if ((time() - filemtime($filePath)) > strtotime(CACHE_DAY . ' days')) {
            Cache::unlinkIfExist($filePath);
            return false;
        }
        return true;*/
    }

    /*static function unlinkIfExist($filePath) {
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }*/
}
