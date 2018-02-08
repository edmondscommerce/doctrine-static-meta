<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

use EdmondsCommerce\DoctrineStaticMeta\Exception\ConfigException;

/**
 * Class SimpleEnv
 *
 * A simplistic take on reading .env files
 *
 * We only require parsing very simple key=value pairs
 *
 * For a more fully featured library, see https://github.com/vlucas/phpdotenv
 *
 * @package EdmondsCommerce\DoctrineStaticMeta
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class SimpleEnv
{
    public static function setEnv(string $filePath, array &$server = null): void
    {
        if (null === $server) {
            $server =& $_SERVER;
        }
        if (!file_exists($filePath)) {
            throw new ConfigException('Env file path '.$filePath.' does not exist');
        }
        $lines = file($filePath);
        foreach ($lines as $line) {
            #skip comments
            if (preg_match('%^\s*#%', $line)) {
                continue;
            }
            preg_match(
                #strip leading spaces
                '%^[[:space:]]*'
                #strip leading `export`
                .'(?:export[[:space:]]+|)'
                #parse out the key and assign to named match
                .'(?<key>[^=]+?)'
                #strip out `=`, possibly with space around it
                .'[[:space:]]*=[[:space:]]*'
                #strip out possible quotes
                ."(?:\"|'|)"
                #parse out the value and assign to named match
                ."(?<value>[^\"']+?)"
                #strip out possible quotes
                ."(?:\"|'|)"
                #string out trailing space to end of line
                .'[[:space:]]*$%',
                $line,
                $matches
            );
            if (5 !== count($matches)) {
                continue;
            }
            list(, $key, $value) = $matches;
            if (empty($value)) {
                continue;
            }
            if (!isset($server[$key])) {
                $server[$key] = $value;
            }
        }
    }
}
