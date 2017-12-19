<?php declare(strict_types=1);
/**
 * Created by Edmonds Commerce
 */

namespace EdmondsCommerce\DoctrineStaticMeta;


class Config implements ConfigInterface
{

    private $config = [];

    public function __construct()
    {
        foreach (static::params as $key) {
            if (!isset($_SERVER[$key])) {
                throw new \Exception(
                    'required config param ' . $key . ' is not set in $_SERVER');
            }
            $this->config[$key] = $_SERVER[$key];
        }
    }

    public function get(string $key): string
    {
        if (!isset(static::params[$key])) {
            throw new \Exception(
                'Invalid config param '
                . $key
                . ', should be one of '
                . print_r(static::params, true));
        }
        return $this->config[$key];

    }

}
