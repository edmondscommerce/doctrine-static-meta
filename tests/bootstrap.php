<?php declare(strict_types=1);
/**
 * Empty out the var path of everything but the .gitignore file
 *
 * Will reinstate an existing .gitignore or default to a standard one
 *
 * @throws ReflectionException
 */
call_user_func(
    function () {
        $varPath = \EdmondsCommerce\DoctrineStaticMeta\Config::getProjectRootDirectory() . '/var';
        if (!is_dir($varPath)) {
            throw new \RuntimeException('var path does not exist at ' . $varPath);
        }
        $filesystem    = new \Symfony\Component\Filesystem\Filesystem();
        $gitIgnorePath = $varPath . '/.gitignore';
        if ($filesystem->exists($gitIgnorePath)) {
            $gitIgnore = file_get_contents($varPath . '/.gitignore');
        } else {
            $gitIgnore = "*\n!.gitignore\n";
        }
        $filesystem->remove($varPath);
        $filesystem->mkdir($varPath);
        file_put_contents($varPath . '/.gitignore', $gitIgnore);
        define('VAR_PATH', realpath($varPath));
    }
);

