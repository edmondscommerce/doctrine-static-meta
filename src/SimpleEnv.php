<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;


class SimpleEnv
{
    public static function setEnv(string $filePath)
    {
        if (!file_exists($filePath)) {
            throw new ConfigException('Env file path ' . $filePath . ' does not exist');
        }
        $env = file_get_contents($filePath);
        preg_match_all('%export (?<key>[^=]+)="(?<value>[^"]+?)"%', $env, $matches);
        if (empty($matches['key'])) {
            throw new \Exception('Failed to parse .env file');
        }
        foreach ($matches['key'] as $k => $key) {
            $_SERVER[$key] = $matches['value'][$k];
        }
    }
}
