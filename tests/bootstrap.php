<?php declare(strict_types=1);

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
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
        $filesystem = new \Symfony\Component\Filesystem\Filesystem();
        if (!is_dir(AbstractIntegrationTest::VAR_PATH)) {
            $filesystem->mkdir(AbstractIntegrationTest::VAR_PATH);
        }
        $gitIgnorePath = AbstractIntegrationTest::VAR_PATH.'/.gitignore';
        if ($filesystem->exists($gitIgnorePath)) {
            $gitIgnore = file_get_contents(AbstractIntegrationTest::VAR_PATH.'/.gitignore');
        } else {
            $gitIgnore = "*\n!.gitignore\n";
        }
        $filesystem->remove(AbstractIntegrationTest::VAR_PATH);
        $filesystem->mkdir(AbstractIntegrationTest::VAR_PATH);
        file_put_contents(AbstractIntegrationTest::VAR_PATH.'/.gitignore', $gitIgnore);
    }
);
