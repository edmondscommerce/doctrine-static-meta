<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

use EdmondsCommerce\DoctrineStaticMeta\Exception\ConfigException;

class SimpleEnv
{
    public static function setEnv(string $filePath)
    {
        if (!file_exists($filePath)) {
            throw new ConfigException('Env file path ' . $filePath . ' does not exist');
        }
        $env = file_get_contents($filePath);
        preg_match_all('%^(?=export |)(?<key>[^=]+)=("|)(?<value>[^"]+?)("|)$%m', $env, $matches);
        if (empty($matches['key'])) {
            throw new ConfigException('Failed to parse .env file');
        }
        foreach ($matches['key'] as $k => $key) {
            $_SERVER[$key] = $matches['value'][$k];
        }
    }
}
