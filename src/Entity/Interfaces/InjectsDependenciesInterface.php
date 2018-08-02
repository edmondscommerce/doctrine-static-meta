<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces;

/**
 * Dependencies are injected with either the constructor (as normal) or using this method when loaded in the repository
 *
 * Interface InjectsDependenciesInterface
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces
 */
interface InjectsDependenciesInterface
{
    public function injectDependencies(...$extraDependencies): void;
}
