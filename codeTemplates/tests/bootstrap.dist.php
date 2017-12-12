<?php declare(strict_types=1);

#You will want to change this
$envFileName = '.env.dsm.dist';

dsmSetupEnv($envFileName);

/**
 * You can change this method if you want, though probably you only need to change the env file name
 *
 * @param string $envFileName
 */
function dsmSetupEnv(string $envFileName)
{
    $envPath = __DIR__ . '/../' . $envFileName;
    if (!file_exists($envPath)) {
        die('Error in ' . __FILE__ . ': env file does not exist at ' . $envPath);
    }
    $env = file_get_contents($envPath);

    preg_match_all('%export (?<key>[^=]+)="(?<value>[^"]+?)"%', $env, $matches);
    if (empty($matches['key'])) {
        die('Error in ' . __FILE__ . ': Failed to parse .env file');
    }
    foreach ($matches['key'] as $k => $key) {
        $_SERVER[$key] = $matches['value'][$k];
    }
}

/**
 * You can replace this function with your own as long as it returns
 * a factory with the required `getEm` method to retrieve a doctrine entity manager
 *
 * This function is called within the tests
 *
 * @return \EdmondsCommerce\DoctrineStaticMeta\EntityManager\EntityManagerFactoryInterface
 */
function dsmGetEntityManagerFactory(): \EdmondsCommerce\DoctrineStaticMeta\EntityManager\EntityManagerFactoryInterface
{
    static $factory;
    if (!$factory) {
        $factory = new \EdmondsCommerce\DoctrineStaticMeta\EntityManager\DevEntityManagerFactory();
    }
    return $factory;
}
