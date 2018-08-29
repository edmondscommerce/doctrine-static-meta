<?php declare(strict_types=1);

use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

/**
 * Empty out the var path of everything but the .gitignore file
 *
 * Will reinstate an existing .gitignore or default to a standard one
 *
 * Set error handler to convert everythign to Exceptions. PHPUnit is supposed to do this but is not reliable
 *
 * @throws ReflectionException
 */
(function () {
    $filesystem = new \Symfony\Component\Filesystem\Filesystem();
    if (!is_dir(AbstractTest::VAR_PATH)) {
        $filesystem->mkdir(AbstractTest::VAR_PATH);
    }
    $gitIgnorePath = AbstractTest::VAR_PATH . '/.gitignore';
    if ($filesystem->exists($gitIgnorePath)) {
        $gitIgnore = file_get_contents(AbstractTest::VAR_PATH . '/.gitignore');
    } else {
        $gitIgnore = "*\n!.gitignore\n";
    }
    $filesystem->remove(AbstractTest::VAR_PATH);
    $filesystem->mkdir(AbstractTest::VAR_PATH);
    file_put_contents(AbstractTest::VAR_PATH . '/.gitignore', $gitIgnore);

    set_error_handler(function ($errno, $errstr, $errfile, $errline) {
        $type = 'ERROR';
        switch ($errno) {
            case E_USER_NOTICE:
                $type = 'NOTICE';
                break;
            case E_USER_WARNING:
                $type = 'WARNING';
                break;
            case E_USER_DEPRECATED:
                $type = 'DEPRECATED';
                if (false !== strpos($errstr, 'Doctrine\Common\ClassLoader is deprecated')) {
                    return true;
                }
                break;
        }
        throw new ErrorException("$type\n$errstr\non line $errline\nin file  $errfile\n");
    });
})();
