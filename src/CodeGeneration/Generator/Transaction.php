<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

/**
 * Class Transaction
 *
 * This class will handle keeping track of created files and then if we have a fatal error, it will remove the created files so we are not left with broken generated code
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator
 */
class Transaction
{
    private static $pathsCreated = [];

    private static $instance;

    private function __construct()
    {
    }

    public static function setPathCreated(string $path)
    {
        if (!self::$instance) {
            self::$instance = new static();
        }
        $realPath = realpath($path);
        if (!$realPath) {
            throw new \Exception("path $path does not seem to exist");
        }
        self::$pathsCreated[$realPath] = true;
    }

    public function __destruct()
    {
        $error = error_get_last();
        if ($error === E_ERROR) {
            $commands = [];
            foreach (self::$pathsCreated as $path) {
                $commands[] = "rm -rf $path";
            }
            echo "\n\nTo clean up created files, run:\n\n" . implode("\n", $commands) . "\n\n\n";
        }
    }

}
