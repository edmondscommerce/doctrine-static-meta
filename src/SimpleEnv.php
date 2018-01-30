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
 */
class SimpleEnv
{
    public static function setEnv(string $filePath, array &$server = null)
    {
        if (null === $server) {
            $server =& $_SERVER;
        }
        if (!file_exists($filePath)) {
            throw new ConfigException('Env file path ' . $filePath . ' does not exist');
        }
        $lines = file($filePath);
        foreach ($lines as $line) {
            preg_match(
                "%^[[:space:]]*(?:export[[:space:]]+|)(?<key>[^=]+?)[[:space:]]*=[[:space:]]*(?:\"|)(?<value>[^\"]+?)(?:\"|)[[:space:]]*$%",
                $line,
                $matches
            );
            if (empty($matches['key'])) {
                continue;
            }
            $server[$matches['key']] = $matches['value'];
        }
    }
}
