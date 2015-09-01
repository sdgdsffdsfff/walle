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
        $cmd = [];
        $workspace = trim(rtrim($this->getConfig()->getDeployment('destination'), '/'));
        foreach ($this->getConfig()->getTasks('pre-deploy') as $task) {
            $cmd[] = preg_replace('#{WORKSPACE}#', $workspace, $task);
        }
        $command = join(' && ', $cmd);
        return $this->runLocalCommand($command, $this->log);
    }

}

