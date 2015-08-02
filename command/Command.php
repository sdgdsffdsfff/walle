<?php
/* *****************************************************************
 * @Author: wushuiyong
 * @Created Time : 五  7/31 22:42:32 2015
 *
 * @File Name: command/Command.php
 * @Description:
 * *****************************************************************/
namespace deploy\command;

abstract class Command {

    /**
     * Handler to the current Log File.
     * @var mixed
     */
    private static $log = null;

    /**
     * Enables or Disables Logging
     * @var boolean
     */
    private static $logEnabled = true;

    /**
     * Config
     * @var \deploy\config\Config
     */
    protected $config;

    abstract public function run();

    final protected function runLocalCommand($command, &$output) {
        self::log('---------------------------------');
        self::log('---- Executing: $ ' . $command);

        $return = 1;
        $log = [];
        exec($command . ' 2>&1', $log, $return);
        $log = implode(PHP_EOL, $log);

        if (!$return) {
            $output = trim($log);
        }

        self::log($log);
        self::log('---------------------------------');

        return !$return;
    }

    final protected function runRemoteCommand($command, &$output, $cdToDirFirst = true) {
        if (0&&$this->config->release('enabled', false) === true) {
            if ($this instanceof IsReleaseAware) {
                $releasesDirectory = '';
            } else {
                $releasesDirectory = '/'
                    . $this->config->release('directory', 'releases')
                    . '/'
                    . $this->config->getReleaseId();
            }
        } else {
            $releasesDirectory = '';
        }

        // if general.yml includes "ssy_needs_tty: true", then add "-t" to the ssh command
        $needs_tty = ''; #($this->config->general('ssh_needs_tty', false) ? '-t' : '');

        foreach ($this->config->hosts as $remoteHost) {
            $localCommand = 'ssh ' . $this->config->getHostIdentityFileOption() . $needs_tty . ' -p ' . $this->config->getHostPort($remoteHost) . ' '
                . '-q -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no '
//            . $this->config->getConnectTimeoutOption()
                . (isset($this->config->deployment['user']) ? $this->config->deployment['user'] . '@' : '')
                . $this->config->getHostName($remoteHost);

            $remoteCommand = str_replace('"', '\"', $command);
            if ($cdToDirFirst) {
                $remoteCommand = 'cd ' . rtrim($this->config->deployment['to'], '/') . $releasesDirectory . ' && ' . $remoteCommand;
            }
            $localCommand .= ' ' . '"sh -c \"' . $remoteCommand . '\""';
            static::log('Run remote command ' . $remoteCommand);

            $this->runLocalCommand($localCommand, $output);
        }
        return true;
    }

    /**
     * Generates the Excludes for rsync
     * @param array $excludes
     * @return string
     */
    protected function excludes($excludes)
    {
        $excludesRsync = '';
        foreach ($excludes as $exclude) {
            $excludesRsync .= ' --exclude=' . escapeshellarg($exclude) . ' ';
        }

        return trim($excludesRsync);
    }

    public static function log($message) {
        if (!self::$logEnabled) return;
        if (self::$log === null) {
            $logFile = realpath(getcwd() . '/runtime/logs') . '/log-' . date('Ymd-His') . '.log';
            self::$log = fopen($logFile, 'w');
        }

        $message = date('Y-m-d H:i:s -- ') . $message;
        fwrite(self::$log, $message . PHP_EOL);

        echo $message . PHP_EOL;
    }


}