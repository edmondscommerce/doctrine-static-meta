<?php declare(strict_types=1);

use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;

/**
 * Empty out the var path of everything but the .gitignore file
 *
 * Will reinstate an existing .gitignore or default to a standard one
 *
 * @throws ReflectionException
 */
call_user_func(
    function () {
        $filesystem    = new \Symfony\Component\Filesystem\Filesystem();
        if (!is_dir(AbstractTest::VAR_PATH)) {
            $filesystem->mkdir(AbstractTest::VAR_PATH);
        }
        $gitIgnorePath = AbstractTest::VAR_PATH.'/.gitignore';
        if ($filesystem->exists($gitIgnorePath)) {
            $gitIgnore = file_get_contents(AbstractTest::VAR_PATH.'/.gitignore');
        } else {
            $gitIgnore = "*\n!.gitignore\n";
        }
        $filesystem->remove(AbstractTest::VAR_PATH);
        $filesystem->mkdir(AbstractTest::VAR_PATH);
        file_put_contents(AbstractTest::VAR_PATH.'/.gitignore', $gitIgnore);
    }
);
