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
        $destination = dirname($this->getConfig()->getReleases('destination'));
        $project = $this->getConfig()->getReleases('project');
        $currentTmp = sprintf('current-%s.tmp', $project);
        $cmd[] = sprintf('ln -sfn %s/%s/%s %s/%s',
            rtrim($this->getConfig()->getReleases('release'), '/'),
            $project,
            $version ? $version : $this->getConfig()->getReleases('release_id'),
            $destination,
            $currentTmp
        );
        $cmd[] = sprintf('chown -h %s %s/%s', $user, $destination, $currentTmp);
        $cmd[] = sprintf('mv -fT %s/%s %s', $destination, $currentTmp, $this->getConfig()->getReleases('destination'));
        $command = join(' && ', $cmd);

        return $this->runRemoteCommand($command, $this->log);
    }
}

