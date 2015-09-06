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

    public function link($version = null) {
        $user = $this->getConfig()->getReleases('user');
        $cmd[] = sprintf('cd %s', $this->getConfig()->getReleases('to'));
        $cmd[] = sprintf('ln -sfn releases/%s/%s current.tmp',
            $this->getConfig()->getDeployment('project'),
            $version ? $version : $this->getConfig()->getReleases('release_id')
        );
        $cmd[] = sprintf('chown -h %s current.tmp', $user);
        $cmd[] = sprintf('mv -fT current.tmp %s', $this->getConfig()->getReleases('symlink'));
        $command = join(' && ', $cmd);

        return $this->runRemoteCommand($command, $this->log);
    }
}

