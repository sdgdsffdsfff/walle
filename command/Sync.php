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

    protected $config;

    public function setEnv($env = 'production') {
        $this->config = new Config($env);
        return $this;
    }

    public function run() {
//        $logL = $logR = null;
//        $local = $this->runLocalCommand('ls ' . $this->config->deployment['from'], $logL);
//        $remote = $this->runRemoteCommand('ls ' . $this->config->deployment['to'], $logR);
//        d($local);
//        d($remote);
        // 目录检查，无则创建

        // 同步文件
        foreach ($this->config->hosts as $remoteHost) {
            $this->_syncFiles($remoteHost);
        }


        // 创建链接指向
    }

    private function _syncFiles($remoteHost) {
        $excludes = $this->config->getExcludes();
        $command = 'rsync -avz '
//            . $strategyFlags . ' '
            . '--rsh="ssh ' . $this->config->getHostIdentityFileOption()
            . '-p' . $this->config->getHostPort($remoteHost) . '" '
            . $this->excludes($excludes) . ' '
//            . $this->excludesListFile($excludesListFilePath) . ' '
            . $this->config->deployment['from'] . ' '
            . (isset($this->config->deployment['user']) ? $this->config->deployment['user'] . '@' : '')
            . $this->config->getHostName($remoteHost) . ':' . $this->config->targetDir;

        $result = $this->runLocalCommand($command, $syncLog);
        return $result;
    }

}

