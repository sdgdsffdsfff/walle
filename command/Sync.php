<?php
/* *****************************************************************
 * @Author: wushuiyong
 * @Created Time : 五  7/31 22:21:23 2015
 *
 * @File Name: command/Sync.php
 * @Description:
 * *****************************************************************/
namespace walle\command;

use walle\command\Command;
use walle\command\Git;

class Sync extends Command {


    /**
     * 初始化部署目录
     *
     * @return bool
     */
    public function initDirector() {
        $command = sprintf('mkdir -p %s',
            rtrim($this->getConfig()->getDeployment('destination'), '/'));
        return $this->runLocalCommand($command, $this->log);
    }
    /**
     * 目录、权限检查
     *
     * @author wushuiyong
     * @param $log
     * @return bool
     */
    public function directorAndPermission() {
        $command = 'mkdir -p ' . $this->getConfig()->targetDir;
        return $this->runRemoteCommand($command, $this->log);

    }

    /**
     * rsync 同步文件
     *
     * @param $remoteHost 远程host，格式：host 、host:port
     * @return bool
     */
    public function syncFiles($remoteHost) {
        $excludes = $this->getConfig()->getExcludes();
        $command = 'rsync -avz '
//            . $strategyFlags . ' '
            . '--rsh="ssh ' . $this->getConfig()->getHostIdentityFileOption()
            . '-p' . $this->getConfig()->getHostPort($remoteHost) . '" '
            . $this->excludes($excludes) . ' '
//            . $this->excludesListFile($excludesListFilePath) . ' '
            . rtrim($this->getConfig()->getDeployment('destination'), '/') . '/ '
            . ($this->getConfig()->getReleases('user') ? $this->getConfig()->getReleases('user') . '@' : '')
            . $this->getConfig()->getHostName($remoteHost) . ':' . $this->getConfig()->targetDir;

        return $this->runLocalCommand($command);
    }

}

