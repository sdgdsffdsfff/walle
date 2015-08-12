<?php
/* *****************************************************************
 * @Author: wushuiyong
 * @Created Time : 二  8/ 4 11:43:53 2015
 *
 * @File Name: command/RomteCmd.php
 * @Description:
 * *****************************************************************/
namespace walle\command;

use walle\command\Command;

class RemoteCmd extends Command {

    public function link() {
        $cmd[] = sprintf('cd %s', $this->getConfig()->getDeployment('to'));
        $cmd[] = sprintf('ln -sfn releases/%s current.tmp', $this->getConfig()->getReleases('releaseId'));
        $cmd[] = 'chown -h edison current.tmp';
        $cmd[] = 'mv -fT current.tmp current';
        $command = join(' && ', $cmd);

        $result = $this->runRemoteCommand($command, $log);
    }
}

