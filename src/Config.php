<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

use Composer\Autoload\ClassLoader;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\TypeHelper;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ConfigException;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Exception;
use RuntimeException;
use ts\Reflection\ReflectionClass;

use function array_key_exists;
use function dirname;
use function get_class;
use function in_array;
use function is_object;

/**
 * Class Config
 *
 * @package EdmondsCommerce\DoctrineStaticMeta
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 */
class Config implements ConfigInterface
{

    private static $projectRootDirectory;
    private $config = [];

    /**
     * Config constructor.
     *
     * @param array|mixed[] $server
     *
     * @throws ConfigException
     * @throws DoctrineStaticMetaException
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
        foreach (self::PARAMS as $key) {
            if (array_key_exists($key, $server)) {
                $this->config[$key] = $server[$key];
                continue;
            }
            $this->config[$key] = $this->get($key);
        }

        $this->validateConfig();
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
        if (
            !isset(static::REQUIRED_PARAMS[$key])
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
     * @throws ConfigException
     * @throws DoctrineStaticMetaException
     */
    private function validateConfig(): void
    {
        $errors     = [];
        $typeHelper = new TypeHelper();
        foreach (ConfigInterface::PARAM_TYPES as $param => $requiredType) {
            $value = $this->get($param);
            if (
                self::TYPE_BOOL === $requiredType
                && is_numeric($value)
                && in_array((int)$value, [0, 1], true)
            ) {
                $this->config[$param] = ($value === 1);
                continue;
            }
            if (is_object($value)) {
                if (!($value instanceof $requiredType)) {
                    $actualType = get_class($value);
                    $errors[]   =
                        ' ERROR  ' . $param . ' is not an instance of the required object [' . $requiredType . ']'
                        . 'currently configured as an object of the class  [' . $actualType . ']';
                }
                continue;
            }
            $actualType = $typeHelper->getType($value);
            if ($actualType !== $requiredType) {
                $valueString = var_export($value, true);
                $errors[]    = ' ERROR  ' . $param . ' is not of the required type [' . $requiredType . ']'
                               . ' currently configured as type [' . $actualType . '] with value: ' . $valueString;
            }
        }
        if ([] !== $errors) {
            throw new ConfigException(implode("\n\n", $errors));
        }
    }

    /**
     * Default Entities path, calculated default
     *
     * @return string
     * @throws DoctrineStaticMetaException
     */
    private function calculateEntitiesPath(): string
    {
        try {
            return self::getProjectRootDirectory() . '/src/Entities';
        } catch (Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in ' . __METHOD__ . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    private function calculateEntitiesCustomDataPath(): string
    {
        try {
            return self::getProjectRootDirectory() . '/tests/Assets/Entity/FakerDataFillers';
        } catch (Exception $e) {
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
                $reflection                 = new ReflectionClass(ClassLoader::class);
                self::$projectRootDirectory = dirname($reflection->getFileName(), 3);
            }

            return self::$projectRootDirectory;
        } catch (Exception $e) {
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
     */
    private function calculateProxyDir(): string
    {
        try {
            $dir = self::getProjectRootDirectory() . '/cache/Proxies';
            if (!is_dir($dir) && !(mkdir($dir, 0777, true) && is_dir($dir))) {
                throw new RuntimeException(
                    'Proxy directory ' . $dir . ' does not exist and failed trying to create it'
                );
            }

            return $dir;
        } catch (Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in ' . __METHOD__ . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @return UnderscoreNamingStrategy
     */
    private function getUnderscoreNamingStrategy(): UnderscoreNamingStrategy
    {
        return new UnderscoreNamingStrategy(CASE_LOWER, true);
    }

    /**
     * @return string
     * @throws DoctrineStaticMetaException
     */
    private function getFilesystemCachePath(): string
    {
        $path = self::getProjectRootDirectory() . '/cache/dsm';
        if (!is_dir($path) && !(mkdir($path, 0777, true) && is_dir($path))) {
            throw new RuntimeException('Failed creating default cache path at ' . $path);
        }

        return $path;
    }

    /**
     * @return string
     * @throws DoctrineStaticMetaException
     */
    private function calculateMigrationsDirectory(): string
    {
        $path = self::getProjectRootDirectory() . '/migrations';
        if (!is_dir($path) && !(mkdir($path, 0777, true) && is_dir($path))) {
            throw new RuntimeException('Failed creating default migrations directory at ' . $path);
        }

        return $path;
    }

    private function calculateProjectRootNamespace(): string
    {
        $namespaceHelper = new NamespaceHelper();
        
        return $namespaceHelper->getProjectRootNamespaceFromComposerJson();
    }
}
