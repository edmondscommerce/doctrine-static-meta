<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Repositories;

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @template T of DSM\Interfaces\EntityInterface
 * @extends DSM\Repositories\AbstractEntityRepository<T>
 */
abstract class AbstractEntityRepository extends DSM\Repositories\AbstractEntityRepository
{

}
