<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

use Composer\Autoload\ClassLoader;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ConfigException;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;

class Config implements ConfigInterface
{

    private static $projectRootDirectory;
    private        $config = [];

    /**
     * Config constructor.
     *
     * @param array $server
     *
     * @throws ConfigException
     */
    public function __construct(array $server)
    {
        foreach (static::REQUIRED_PARAMS as $key) {
            if (!array_key_exists($key, $server)) {
                throw new ConfigException(
                    'required config param ' . $key . ' is not set in $server'
                );
            }
            $this->config[$key] = $server[$key];
        }
        foreach (static::OPTIONAL_PARAMS_WITH_DEFAULTS as $key => $value) {
            if (array_key_exists($key, $server)) {
                $this->config[$key] = $server[$key];
            }
        }
        foreach (static::OPTIONAL_PARAMS_WITH_CALCULATED_DEFAULTS as $key => $value) {
            if (array_key_exists($key, $server)) {
                $this->config[$key] = $server[$key];
            }
        }
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed|string
     * @throws DoctrineStaticMetaException
     */
    public function get(string $key, $default = ConfigInterface::NO_DEFAULT_VALUE)
    {
        if (!isset(static::REQUIRED_PARAMS[$key])
            && !isset(static::OPTIONAL_PARAMS_WITH_DEFAULTS[$key])
            && !isset(static::OPTIONAL_PARAMS_WITH_CALCULATED_DEFAULTS[$key])
        ) {
            throw new ConfigException(
                'Invalid config param '
                . $key
                . ', should be one of '
                . print_r(static::PARAMS, true)
            );
        }
        if (isset($this->config[$key])) {
            return $this->config[$key];
        }
        if (ConfigInterface::NO_DEFAULT_VALUE !== $default) {
            return $default;
        }
        if (isset(static::OPTIONAL_PARAMS_WITH_DEFAULTS[$key])) {
            return static::OPTIONAL_PARAMS_WITH_DEFAULTS[$key];
        }
        if (isset(static::OPTIONAL_PARAMS_WITH_CALCULATED_DEFAULTS[$key])) {
            $method = static::OPTIONAL_PARAMS_WITH_CALCULATED_DEFAULTS[$key];

            return $this->$method();
        }
        throw new ConfigException(
            'No config set for param ' . $key . ' and no default provided'
        );
    }

    /**
     * Default Entities path, calculated default
     *
     * @return string
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function calculateEntitiesPath(): string
    {
        try {
            return self::getProjectRootDirectory() . '/src/Entities';
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in ' . __METHOD__ . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Get the absolute path to the root of the current project
     *
     * It does this by working from the Composer autoloader which we know will be in a certain place in `vendor`
     *
     * @return string
     * @throws DoctrineStaticMetaException
     */
    public static function getProjectRootDirectory(): string
    {
        try {
            if (null === self::$projectRootDirectory) {
                $reflection                 = new \ts\Reflection\ReflectionClass(ClassLoader::class);
                self::$projectRootDirectory = \dirname($reflection->getFileName(), 3);
            }

            return self::$projectRootDirectory;
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in ' . __METHOD__ . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Default Entities path, calculated default
     *
     * @return string
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function calculateProxyDir(): string
    {
        try {
            $dir = self::getProjectRootDirectory() . '/cache/Proxies';
            if (!is_dir($dir) && !(mkdir($dir, 0777, true) && is_dir($dir))) {
                throw new \RuntimeException(
                    'Proxy directory ' . $dir . ' does not exist and failed trying to create it'
                );
            }

            return $dir;
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in ' . __METHOD__ . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @return UnderscoreNamingStrategy
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function getUnderscoreNamingStrategy(): UnderscoreNamingStrategy
    {
        return new UnderscoreNamingStrategy();
    }
}
