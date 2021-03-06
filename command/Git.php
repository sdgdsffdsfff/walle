<?php
/* *****************************************************************
 * @Author: wushuiyong
 * @Created Time : 日  8/ 2 10:43:15 2015
 *
 * @File Name: command/Git.php
 * @Description:
 * *****************************************************************/
namespace walle\command;

use walle\command\Command;

class Git extends Command {

    public function updateRepo() {
        $destination = $this->getConfig()->getDeployment('destination');
        // 存在git目录，直接pull
        if (file_exists($destination)) {
            $cmd[] = sprintf('cd %s ', $destination);
            $cmd[] = sprintf('/usr/bin/env git fetch --all');
            $cmd[] = sprintf('/usr/bin/env git reset --hard origin/%s', $this->getConfig()->getScm('branch'));
            $cmd[] = sprintf('/usr/bin/env git checkout %s', $this->getConfig()->getScm('branch'));
            $command = join(' && ', $cmd);
            return $this->runLocalCommand($command);
        }
        // 不存在，则先checkout
        else {
            $parentDir = dirname($destination);
            $baseName = basename($destination);
            $cmd[] = sprintf('mkdir -p %s ', $destination);
            $cmd[] = sprintf('cd %s ', $parentDir);
            $cmd[] = sprintf('/usr/bin/env git clone %s %s', $this->getConfig()->getScm('url'), $baseName);
            $cmd[] = sprintf('cd %s', $destination);
            $cmd[] = sprintf('/usr/bin/env git checkout %s', $this->getConfig()->getScm('branch'));
            $command = join(' && ', $cmd);
            return $this->runLocalCommand($command);
        }
    }

    /**
     * 回滚到指定commit版本
     *
     * @param string $commit
     * @return bool
     */
    public function rollback($commit) {
        $destination = $this->getConfig()->getDeployment('destination');
        $cmd[] = sprintf('cd %s ', $destination);
        $cmd[] = sprintf('/usr/bin/env git reset %s', $commit);
        $cmd[] = '/usr/bin/env git checkout .';
        $command = join(' && ', $cmd);

        return $this->runLocalCommand($command);
    }

    /**
     * 获取提交历史
     *
     * @return array
     */
    public function getCommitList($count = 20) {
        $this->updateRepo();
        $destination = $this->getConfig()->getDeployment('destination');
        $cmd[] = sprintf('cd %s ', $destination);
        $cmd[] = '/usr/bin/env git log -' . $count . ' --pretty="%h - %an %s" ';
        $command = join(' && ', $cmd);
        $result = $this->runLocalCommand($command);
        $history = [];
        if ($result) {
            $list = explode("\n", $this->getExeLog());
            foreach ($list as $item) {
                $commitId = substr($item, 0, strpos($item, '-') - 1);
                $history[] = [
                    'id' => $commitId,
                    'message'  => $item,
                ];
            }
        }
        return $history;
    }

    /**
     * 获取tag记录
     *
     * @return array
     */
    public function getTagList($count = 20) {
        $this->updateRepo();
        $destination = $this->getConfig()->getDeployment('destination');
        $cmd[] = sprintf('cd %s ', $destination);
        $cmd[] = '/usr/bin/env git tag -l -n' . $count;
        $command = join(' && ', $cmd);
        $result = $this->runLocalCommand($command);
        $history = [];
        if ($result) {
            $list = explode("\n", $this->getExeLog());
            foreach ($list as $item) {
                $commitId = substr($item, 0, strpos($item, ' '));
                $history[] = [
                    'id' => $commitId,
                    'message'  => $item,
                ];
            }
        }
        return $history;
    }

}
