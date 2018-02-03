<?php declare(strict_types=1);

use EdmondsCommerce\DoctrineStaticMeta\Config;

/**
 * Empty out the var path of everything but the .gitignore file
 *
 * Will reinstate an existing .gitignore or default to a standard one
 *
 * @throws ReflectionException
 */
call_user_func(
    function () {
        define('VAR_PATH', realpath(Config::getProjectRootDirectory().'/var'));
        if (!is_dir(VAR_PATH)) {
            throw new \RuntimeException('var path does not exist at '.VAR_PATH);
        }
        $filesystem    = new \Symfony\Component\Filesystem\Filesystem();
        $gitIgnorePath = VAR_PATH.'/.gitignore';
        if ($filesystem->exists($gitIgnorePath)) {
            $gitIgnore = file_get_contents(VAR_PATH.'/.gitignore');
        } else {
            $gitIgnore = "*\n!.gitignore\n";
        }
        $filesystem->remove(VAR_PATH);
        $filesystem->mkdir(VAR_PATH);
        file_put_contents(VAR_PATH.'/.gitignore', $gitIgnore);
    }
);
