<?php
/* *****************************************************************
 * @Author: wushuiyong
 * @Created Time : 五  7/31 22:26:36 2015
 *
 * @File Name: config/Config.php
 * @Description:
 * *****************************************************************/
namespace deploy\config;

use deploy\config\Exception\ParseException;

class Config {

    public static function getEnv($env = 'production') {
        $file = dirname(__DIR__) . "/../yml/config/env/{$env}.yml";
        return static::parse($file);
    }

    /**
     * Parses .yml into a PHP array.
     *
     * The parse method, when supplied with a .yml stream (string or file),
     * will do its best to convert .yml in a file into a PHP array.
     *
     *  Usage:
     *  <code>
     *   $array = .yml::parse('config.yml');
     *   print_r($array);
     *  </code>
     *
     * As this method accepts both plain strings and file names as an input,
     * you must validate the input before calling this method. Passing a file
     * as an input is a deprecated feature and will be removed in 3.0.
     *
     * @param string $input Path to a .yml file or a string containing .yml
     * @param bool $exceptionOnInvalidType True if an exception must be thrown on invalid types false otherwise
     * @param bool $objectSupport True if object support is enabled, false otherwise
     *
     * @return array The .yml converted to a PHP array
     *
     * @throws ParseException If the .yml is not valid
     *
     * @api
     */
    public static function parse($input, $exceptionOnInvalidType = false, $objectSupport = false)
    {
        // if input is a file, process it
        $file = '';
        if (strpos($input, "\n") === false && is_file($input)) {
            if (false === is_readable($input)) {
                throw new ParseException(sprintf('Unable to parse "%s" as the file is not readable.', $input));
            }

            $file = $input;
            $input = file_get_contents($file);
        }

        $conf = new Parser();

        try {
            return $conf->parse($input, $exceptionOnInvalidType, $objectSupport);
        } catch (ParseException $e) {
            if ($file) {
                $e->setParsedFile($file);
            }

            throw $e;
        }
    }
}
