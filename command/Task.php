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

class Task extends Command {

    /**
     * 初始化部署目录
     *
     * @return bool
     */
    public function preDeploy() {
        $tasks = $this->getConfig()->getTasks('pre-deploy');
        if (empty($tasks)) return true;

        $cmd = [];
        $workspace = trim(rtrim($this->getConfig()->getDeployment('destination'), '/'));
        $pattern = [
            '#{WORKSPACE}#',
        ];
        $replace = [
            $workspace,
        ];

        foreach ($tasks as $task) {
            $cmd[] = preg_replace($pattern, $replace, $task);
        }
        $command = join(' && ', $cmd);
        return $this->runLocalCommand($command, $this->log);
    }

    /**
     * release时任务
     *
     * @return bool
     */
    public function postRelease() {
        $tasks = $this->getConfig()->getTasks('post-release');
        if (empty($tasks)) return true;

        $cmd = [];
        $workspace = trim(rtrim($this->getConfig()->getReleases('destination'), '/'));
        $version   = $this->getConfig()->targetDir;
        $pattern = [
            '#{WORKSPACE}#',
            '#{VERSION}#',
        ];
        $replace = [
            $workspace,
            $version,
        ];
        foreach ($tasks as $task) {
            $cmd[] = preg_replace($pattern, $replace, $task);
        }
        $command = join(' && ', $cmd);
        return $this->runLocalCommand($command, $this->log);
    }

}

