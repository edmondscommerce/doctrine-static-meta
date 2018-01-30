<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

use EdmondsCommerce\DoctrineStaticMeta\Exception\ConfigException;

class SimpleEnv
{
    public static function setEnv(string $filePath, array &$server = null)
    {
        if (null === $server) {
            $server = $_SERVER;
        }
        if (!file_exists($filePath)) {
            throw new ConfigException('Env file path ' . $filePath . ' does not exist');
        }
        $env = file_get_contents($filePath);
        preg_match_all("%^[[:space:]]*(export[[:space:]]+|)(?<key>[^=]+?)[[:space:]]*=[[:space:]]*(\"|)(?<value>[^\"]+?)(\"|)[[:space:]]*$%m", $env, $matches);
        if (empty($matches['key'])) {
            throw new ConfigException('Failed to parse .env file');
        }
        foreach ($matches['key'] as $k => $key) {
            $server[$key] = $matches['value'][$k];
        }
    }
}
