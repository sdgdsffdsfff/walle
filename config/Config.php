<?php
/* *****************************************************************
 * @Author: wushuiyong
 * @Created Time : 五  7/31 22:26:36 2015
 *
 * @File Name: config/Config.php
 * @Description:
 * *****************************************************************/
namespace deploy\config;

class Config {

    public static function getEnv($env = 'production') {
        $config = statis::parse($env);
    }
}
