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
        // 遇到回滚，则使用回滚的版本version
        $linkFrom = $version
            ? dirname($this->getConfig()->targetDir) . '/' . $version
            : $this->getConfig()->targetDir;
        $cmd[] = sprintf('ln -sfn %s %s/%s', $linkFrom, $destination, $currentTmp);
        $cmd[] = sprintf('chown -h %s %s/%s', $user, $destination, $currentTmp);
        $cmd[] = sprintf('mv -fT %s/%s %s', $destination, $currentTmp, $this->getConfig()->getReleases('destination'));
        $command = join(' && ', $cmd);

        return $this->runRemoteCommand($command);
    }

    public function getFileMd5($file) {
        $cmd[] = "test -f /usr/bin/md5sum && md5sum {$file}";
        $command = join(' && ', $cmd);

        return $this->runRemoteCommand($command);
    }
}

