<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

/**
 * Class FileCreationTransaction
 *
 * This class will handle keeping track of created files and then if we have a fatal error, it will remove the created
 * files so we are not left with broken generated code
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator
 */
class FileCreationTransaction
{
    private static $pathsCreated = [];

    private static $registered;

    private static $startTime;

    private static function registerShutdownFunction()
    {
        self::$startTime = microtime(true);
        register_shutdown_function(
            function () {
                $error = error_get_last();
                if ($error === E_ERROR) {
                    $sinceTimeSeconds = ceil(microtime(true) - self::$startTime);
                    $sinceTimeMinutes = ceil($sinceTimeSeconds / 60); // why, because of xdebug break - you could easily spend over 1 minute stepping through
                    $dirsToSearch     = [];
                    foreach (self::$pathsCreated as $path) {
                        if (false !== strpos($path, '.php')) {
                            $path = dirname($path);
                        }
                        $dirsToSearch[] = $path;
                    }
                    $findCommand   = "find " . implode(' ', $dirsToSearch) . "  -mmin -$sinceTimeMinutes";
                    $line          = str_repeat('-', 15);
                    $deleteCommand = "$findCommand -exec rm -rf";
                    echo "\n$line\n"
                        . "\n\nUnclean File Creation Transaction:"
                        . "\n\nTo find created files:\n$findCommand"
                        . "\n\nTo remove created files:\n$deleteCommand"
                        . "\n\n$line\n\n";
                }
            }
        );
        return true;
    }


    /**
     * @param string $path The absolute path to the created file or folder
     *
     * @throws \Exception if the path does not exist
     */
    public static function setPathCreated(string $path)
    {
        if (!self::$registered) {
            self::$registered = self::registerShutdownFunction();
        }
        $realPath = realpath($path);
        if (!$realPath) {
            throw new \Exception("path $path does not seem to exist");
        }
        self::$pathsCreated[$realPath] = $realPath;
    }
}
