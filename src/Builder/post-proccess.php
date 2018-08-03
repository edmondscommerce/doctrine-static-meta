<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Builder;

use EdmondsCommerce\DoctrineStaticMeta\SimpleEnv;
use EdmondsCommerce\VaultEntities\Entities\Portfolio\Holding;
use EdmondsCommerce\VaultEntities\Entities\PortfolioTest;
use EdmondsCommerce\VaultEntities\Entity\Interfaces\Portfolio\HoldingInterface;
use EdmondsCommerce\VaultEntities\Entity\Relations\Commodity\Traits\HasCommodity\HasCommodityManyToOne;
use EdmondsCommerce\VaultEntities\Entity\Relations\Commodity\Traits\HasCommodity\HasCommodityUnidirectionalManyToOne;
use EdmondsCommerce\VaultEntities\Entity\Relations\Portfolio\Traits\HasPortfolio\HasPortfolioManyToOne;
use EdmondsCommerce\VaultEntities\Extensions\Entities\Portfolio\AvoidRemovingRequiredAssociationsTrait;
use EdmondsCommerce\VaultEntities\Extensions\Entities\Portfolio\Holding\CompositeIdField;
use EdmondsCommerce\VaultEntities\Extensions\Entity\Fields\Interfaces\PrimaryKey\CompositeIdFieldInterface;

require __DIR__ . '/../vendor/autoload.php';

SimpleEnv::setEnv(__DIR__ . '/../.env');
$containerBuilder = new \Symfony\Component\DependencyInjection\ContainerBuilder();
$containerBuilder->autowire(Builder::class)->setPublic(true);
(new \EdmondsCommerce\DoctrineStaticMeta\Container())->addConfiguration($containerBuilder, $_SERVER);
$containerBuilder->compile();

/** @var Builder $builder */
$builder = $containerBuilder->get(Builder::class);

/* You can now use the builder process the classes */
