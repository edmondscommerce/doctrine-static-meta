<?php declare(strict_types=1);

use Doctrine\ORM\Tools\Console\ConsoleRunner;

require __DIR__.'/../vendor/autoload.php';

$entityManager = (new \EdmondsCommerce\DoctrineStaticMeta\EntityManager\DevEntityManagerFactory())->getEm(false);

return ConsoleRunner::createHelperSet($entityManager);
