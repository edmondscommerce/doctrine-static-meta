<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

class Config implements ConfigInterface
{

    private $config = [];

    private $projectRootDirectory;

    public function __construct()
    {
        foreach (static::requiredParams as $key) {
            if (!isset($_SERVER[$key])) {
                throw new \Exception(
                    'required config param ' . $key . ' is not set in $_SERVER');
            }
            $this->config[$key] = $_SERVER[$key];
        }
        foreach (static::optionalParamsWithDefaults as $key => $value) {
            if (array_key_exists($key, $_SERVER)) {
                $this->config[$key] = $_SERVER[$key];
            }
        }
        foreach (static::optionalParamsWithCalculatedDefaults as $key => $value) {
            if (array_key_exists($key, $_SERVER)) {
                $this->config[$key] = $_SERVER[$key];
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
            throw new \Exception(
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
            throw new \Exception(
                'No config set for param ' . $key . ' and no default provided'
            );
        }
        return $this->config[$key];
    }

    public function getProjectRootDirectory(): string
    {
        if (null === $this->projectRootDirectory) {
            $reflection = new \ReflectionClass(\Composer\Autoload\ClassLoader::class);
            $vendorDir = dirname(dirname($reflection->getFileName()));
            $this->projectRootDirectory = dirname($vendorDir);
        }
        return $this->projectRootDirectory;
    }

    /**
     * Default Entities path, calculated default
     * @return string
     */
    protected function calculateEntitiesPath(): string
    {
        return $this->getProjectRootDirectory() . '/src/Entities';
    }

}
