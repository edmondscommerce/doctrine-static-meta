<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

class Config implements ConfigInterface
{

    private $config = [];

    public function __construct()
    {
        foreach (static::requiredParams as $key) {
            if (!isset($_SERVER[$key])) {
                throw new \Exception(
                    'required config param ' . $key . ' is not set in $_SERVER');
            }
            $this->config[$key] = $_SERVER[$key];
        }
    }

    public function get(string $key, $default = null)
    {
        if (!isset(static::requiredParams[$key]) && !isset(static::optionalParams)) {
            throw new \Exception(
                'Invalid config param '
                . $key
                . ', should be one of '
                . print_r(static::requiredParams, true));
        }
        if (!isset($this->config[$key])) {
            if (null !== $default) {
                return $default;
            }
            throw new \Exception(
                'No config set for param ' . $key . ' and no default provided'
            );
        }
        return $this->config[$key];
    }

}
