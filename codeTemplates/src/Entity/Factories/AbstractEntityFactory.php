<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Factories;

// phpcs:disable -- line length
use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;

// phpcs:enable

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class AbstractEntityFactory
{
    public function __construct(
        protected DSM\Factory\EntityFactoryInterface $entityFactory,
        EntityManagerInterface $entityManager
    ) {
        $this->entityFactory->setEntityManager($entityManager);
    }
}
