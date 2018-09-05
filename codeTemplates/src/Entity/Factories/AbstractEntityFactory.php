<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Repositories;

// phpcs:disable -- line length
use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class AbstractEntityFactory
{
    // phpcs:enable
    /**
     * @var DSM\Factory\EntityFactory
     */
    protected $entityFactory;

    public function __construct(DSM\Factory\EntityFactory $entityFactory)
    {
        $this->entityFactory = $entityFactory;
    }
}
