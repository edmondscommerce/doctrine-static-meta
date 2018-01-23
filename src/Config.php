<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

use EdmondsCommerce\DoctrineStaticMeta\Exception\ConfigException;

class Config implements ConfigInterface
{

    private $config = [];

    private static $projectRootDirectory;

    public function __construct(array $server)
    {
        foreach (static::requiredParams as $key) {
            if (!isset($server[$key])) {
                throw new ConfigException(
                    'required config param ' . $key . ' is not set in $server');
            }
            $this->config[$key] = $server[$key];
        }
        foreach (static::optionalParamsWithDefaults as $key => $value) {
            if (array_key_exists($key, $server)) {
                $this->config[$key] = $server[$key];
            }
        }
        foreach (static::optionalParamsWithCalculatedDefaults as $key => $value) {
            if (array_key_exists($key, $server)) {
                $this->config[$key] = $server[$key];
            }
        }
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed|string
     * @throws \Exception
     */
    public function get(string $key, $default = ConfigInterface::noDefaultValue)
    {
        if (!isset(static::requiredParams[$key])
            && !isset(static::optionalParamsWithDefaults[$key])
            && !isset(static::optionalParamsWithCalculatedDefaults[$key])
        ) {
            throw new ConfigException(
                'Invalid config param '
                . $key
                . ', should be one of '
                . print_r(static::requiredParams, true));
        }
        if (!isset($this->config[$key])) {
            if (ConfigInterface::noDefaultValue !== $default) {
                return $default;
            } elseif (isset(static::optionalParamsWithDefaults[$key])) {
                return static::optionalParamsWithDefaults[$key];
            } elseif (isset(static::optionalParamsWithCalculatedDefaults[$key])) {
                $method = static::optionalParamsWithCalculatedDefaults[$key];
                return $this->$method();
            }
            throw new ConfigException(
                'No config set for param ' . $key . ' and no default provided'
            );
        }
        return $this->config[$key];
    }

    /**
     * Get the absolute path to the root of the current project
     *
     * It does this by working from the Composer autoloader which we know will be in a certain place in `vendor`
     *
     * @return string
     * @throws \ReflectionException
     */
    public static function getProjectRootDirectory(): string
    {
        if (null === self::$projectRootDirectory) {
            $reflection = new \ReflectionClass(\Composer\Autoload\ClassLoader::class);
            $vendorDir = dirname(dirname($reflection->getFileName()));
            self::$projectRootDirectory = dirname($vendorDir);
        }
        return self::$projectRootDirectory;
    }

    /**
     * Default Entities path, calculated default
     * @return string
     * @throws \ReflectionException
     */
    protected function calculateEntitiesPath(): string
    {
        return self::getProjectRootDirectory() . '/src/Entities';
    }

}
