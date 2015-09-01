<?php
/* *****************************************************************
 * @Author: wushuiyong
 * @Created Time : 五  7/31 22:42:32 2015
 *
 * @File Name: command/Command.php
 * @Description:
 * *****************************************************************/
namespace walle\command;

use walle\config\Config;

abstract class Command {

    protected static $LOGDIR = '';
    /**
     * Handler to the current Log File.
     * @var mixed
     */
    protected static $logFile = null;


    /**
     * Enables or Disables Logging
     * @var boolean
     */
    private static $logEnabled = true;

    /**
     * Config
     * @var \walle\config\Config
     */
    protected $config;

    protected $log = null;

//    abstract public function run();

    final protected function runLocalCommand($command, &$output) {
        file_put_contents('/tmp/cmd', $command.PHP_EOL.PHP_EOL, 8);
        self::log('---------------------------------');
        self::log('---- Executing: $ ' . $command);

        $return = 1;
        $log = [];
        exec($command . ' 2>&1', $log, $return);
        $log = implode(PHP_EOL, $log);

        $output = trim($log);

        self::log($log);
        self::log('---------------------------------');

        return !$return;
    }

    final protected function runRemoteCommand($command, &$output, $cdToDirFirst = true) {
        if (0&&$this->getConfig()->getReleases('enabled', false) === true) {
            if ($this instanceof IsReleaseAware) {
                $releasesDirectory = '';
            } else {
                $releasesDirectory = '/'
                    . $this->getConfig()->getReleases('directory', 'releases')
                    . '/'
                    . $this->getConfig()->getReleaseId();
            }
        } else {
            $releasesDirectory = '';
        }

        // if general.yml includes "ssy_needs_tty: true", then add "-t" to the ssh command
        $needs_tty = ''; #($this->getConfig()->general('ssh_needs_tty', false) ? '-t' : '');

        foreach ($this->getConfig()->getHosts() as $remoteHost) {
            $localCommand = 'ssh ' . $this->getConfig()->getHostIdentityFileOption() . $needs_tty . ' -p ' . $this->getConfig()->getHostPort($remoteHost) . ' '
                . '-q -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no '
//            . $this->getConfig()->getConnectTimeoutOption()
                . ($this->getConfig()->getReleases('user') ? $this->getConfig()->getReleases('user') . '@' : '')
                . $this->getConfig()->getHostName($remoteHost);

            $remoteCommand = str_replace('"', '\"', $command);
            if ($cdToDirFirst) {
//                $remoteCommand = 'cd ' . rtrim($this->getConfig()->getDeployment('to'), '/') . $releasesDirectory . ' && ' . $remoteCommand;
            }
            $localCommand .= ' ' . '"sh -c \"' . $remoteCommand . '\""';
            static::log('Run remote command ' . $remoteCommand);

            $ret = $this->runLocalCommand($localCommand, $this->log);
            if (!$ret) return false;
        }
        return true;
    }

    public function setConfig($env = 'production') {
        if ($env instanceof \walle\config\Config) {
            $this->config = $env;
            static::$LOGDIR = $this->config->getDeployment('log-dir');
        } else {
            $this->config = new Config($env);
        }
        return $this;
    }

    protected function getConfig() {
        return $this->config;
    }

    /**
     * Generates the Excludes for rsync
     * @param array $excludes
     * @return string
     */
    protected function excludes($excludes) {
        $excludesRsync = '';
        foreach ($excludes as $exclude) {
            $excludesRsync .= ' --exclude=' . escapeshellarg($exclude) . ' ';
        }

        return trim($excludesRsync);
    }

    public static function log($message) {
        if (!self::$logEnabled) return;
        if (self::$logFile === null) {
            $logFile = realpath(self::$LOGDIR) . '/log-' . date('Ymd-His') . '.log';
            self::$logFile = fopen($logFile, 'w');
        }

        $message = date('Y-m-d H:i:s -- ') . $message;
        fwrite(self::$logFile, $message . PHP_EOL);
    }

    /**
     * 获取执行log
     *
     * @author wushuiyong
     * @return string
     */
    public function getLog() {
        return $this->log;
    }

    public static function getMs() {
        return intval(microtime(true) * 1000);
    }

}
