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
    /**
     * @var DSM\Factory\EntityFactoryInterface
     */
    protected $entityFactory;

    public function __construct(
        DSM\Factory\EntityFactoryInterface $entityFactory,
        EntityManagerInterface $entityManager
    ) {
        $this->entityFactory = $entityFactory;
        $this->entityFactory->setEntityManager($entityManager);
    }
}
