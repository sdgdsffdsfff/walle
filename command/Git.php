<?php
/* *****************************************************************
 * @Author: wushuiyong
 * @Created Time : 日  8/ 2 10:43:15 2015
 *
 * @File Name: command/Git.php
 * @Description:
 * *****************************************************************/
namespace deploy\command;

use deploy\command\Command;

class Git extends Command {

    public function updateRepo() {
        $log = null;
        $destination = $this->getConfig()->getDeployment('from');
        // 存在git目录，直接pull
        if (file_exists($destination)) {
            $cmd[] = sprintf('cd %s ', $destination);
            $cmd[] = sprintf('/usr/bin/env git fetch --all');
            $cmd[] = sprintf('/usr/bin/env git reset --hard origin/%s', $this->getConfig()->getScm('branch'));
            $command = join(' && ', $cmd);
            $result = $this->runLocalCommand($command, $log);
        }
        // 不存在，则先checkout
        else {
            $parentDir = dirname($destination);
            $baseName = basename($destination);
            $cmd[] = sprintf('cd %s ', $parentDir);
            $cmd[] = sprintf('/usr/bin/env git clone %s %s',
                $this->getConfig()->getScm('url'), $baseName);
            $command = join(' && ', $cmd);
            $result = $this->runLocalCommand($command, $log);
        }

    }

    /**
     * 回滚到指定commit版本
     *
     * @param string $commit
     * @return bool
     */
    public function rollback($commit) {
        $destination = $this->getConfig()->getDeployment('from');
        $cmd[] = sprintf('cd %s ', $destination);
        $cmd[] = sprintf('/usr/bin/env git reset %s', $commit);
        $cmd[] = '/usr/bin/env git checkout .';
        $command = join(' && ', $cmd);
        $result = $this->runLocalCommand($command, $log);
        return $result;
    }

    public function getCommitList() {
        $destination = $this->getConfig()->getDeployment('from');
        $cmd[] = sprintf('cd %s ', $destination);
        $cmd[] = '/usr/bin/env git log --pretty="%h - %an %s"  --since="2008-10-01"  --no-merges';
        $command = join(' && ', $cmd);
        $list = [];
        $result = $this->runLocalCommand($command, $list);
        if ($result && $list) {
            return explode("\n", $list);
        }
        return [];
    }

}
