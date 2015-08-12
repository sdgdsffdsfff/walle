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


    public function run() {

        // 记录此次操作入数据库（可以迁移到外层去做）

        // 目录检查，无则创建

        // 权限检查

        // 更新代码文件
        $git = new Git();
        $git->setConfig($this->getConfig());
//        $git->updateRepo();

        // 回滚版本
//        $commit = '425d360eb4a1fa26e0cef7d858d98c2992a40bde';
//        $git->rollback($commit);

        // 查看提交历史
        $list = $git->getCommitList();
        echo '+++++++++',PHP_EOL;
        d($list);
        die;


        // 同步文件
        foreach ($this->getConfig()->getHosts() as $remoteHost) {
            $this->syncFiles($remoteHost);
        }


        // 创建链接指向
        $remote = new RemoteCmd();
        $remote->setConfig($this->getConfig());
        $remote->link();
    }

    public function directorAndPermission() {
        $command = 'mkdir -p %s' . $this->getConfig()->targetDir;
        if (!$this->runRemoteCommand($command, $log)) {
            $command = 'mkdir -p ' . $this->getConfig()->targetDir;
            $result = $this->runRemoteCommand($command, $log);
            dd($result);
        }

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
            . $this->getConfig()->getDeployment('from') . ' '
            . ($this->getConfig()->getDeployment('user') ? $this->getConfig()->getDeployment('user') . '@' : '')
            . $this->getConfig()->getHostName($remoteHost) . ':' . $this->getConfig()->targetDir;

        return $this->runLocalCommand($command, $syncLog);
    }

}

