<?php
/* *****************************************************************
 * @Author: wushuiyong
 * @Created Time : 五  7/31 22:21:23 2015
 *
 * @File Name: command/Sync.php
 * @Description:
 * *****************************************************************/
namespace deploy\command;

use deploy\command\Command;
use deploy\config\Config;

class Sync extends Command {

    public function setEnv($env = 'production') {
        $this->config = Config::getEnv($env);
    }

    public function run() {
        echo __METHOD__, PHP_EOL;
    }
}

