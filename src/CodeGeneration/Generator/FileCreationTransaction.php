<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;

/**
 * Class FileCreationTransaction
 *
 * This class will handle keeping track of created files and then if we have a fatal error,
 * it will allow us to more easily remove the created files so we are not left with broken generated code
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator
 */
class FileCreationTransaction
{
    /**
     * @var array List of paths that have been created
     */
    private static $pathsCreated = [];

    /**
     * @var bool Have we registered the shutdown function
     */
    private static $registered = false;

    /**
     * @var float Time the first file was created in float unix time
     */
    private static $startTime = 0.0;

    public static function getTransaction(): array
    {
        return self::$pathsCreated;
    }

    /**
     * @param string $path The absolute path to the created file or folder
     *
     * @throws DoctrineStaticMetaException if the path does not exist
     */
    public static function setPathCreated(string $path): void
    {
        if (!self::$registered) {
            self::$registered = self::registerShutdownFunction();
        }
        $realPath = realpath($path);
        if (!$realPath) {
            throw new DoctrineStaticMetaException("path $path does not seem to exist");
        }
        self::$pathsCreated[$realPath] = $realPath;
    }

    /**
     * Registers our shutdown function. Will attempt to echo out the dirty file clean up commands on a fatal error
     *
     * @return bool
     */
    private static function registerShutdownFunction(): bool
    {
        self::$startTime = microtime(true);
        register_shutdown_function(
            static function () {
                $error = error_get_last();
                if ($error === E_ERROR && count(self::$pathsCreated) > 0) {
                    self::echoDirtyTransactionCleanupCommands();
                }
            }
        );

        return true;
    }

    /**
     * Echos out bash find commands to find and delete created paths
     *
     * @param bool|resource $handle
     */
    public static function echoDirtyTransactionCleanupCommands(bool $handle = STDERR): void
    {
        if (0 === count(self::$pathsCreated)) {
            return;
        }
        $sinceTimeSeconds = ceil(microtime(true) - self::$startTime);
        // why, because of xdebug break - you could easily spend over 1 minute stepping through
        $sinceTimeMinutes = ceil($sinceTimeSeconds / 60);
        $pathsToSearch    = [];
        foreach (self::$pathsCreated as $path) {
            $realPath = realpath($path);
            if ($realPath) {
                $pathsToSearch[$realPath] = $realPath;
            }
        }
        if (0 === count($pathsToSearch)) {
            return;
        }
        $findCommand   = 'find ' . implode(' ', $pathsToSearch) . "  -mmin -$sinceTimeMinutes";
        $line          = str_repeat('-', 15);
        $deleteCommand = "$findCommand -exec rm -rf";
        fwrite(
            $handle,
            "\n$line\n"
            . "\n\nUnclean File Creation Transaction:"
            . "\n\nTo find created files:\n$findCommand"
            . "\n\nTo remove created files:\n$deleteCommand"
            . "\n\n$line\n\n"
        );
    }

    /**
     * If the transaction is successful, we can clear out our log of created files
     */
    public static function markTransactionSuccessful(): void
    {
        self::$pathsCreated = [];
    }
}
