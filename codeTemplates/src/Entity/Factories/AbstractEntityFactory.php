<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Repositories;

// phpcs:disable -- line length
use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;

// phpcs:enable

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class AbstractEntityFactory
{

    /**
     * @var DSM\Factory\EntityFactory
     */
    protected $entityFactory;

    public function __construct(DSM\Factory\EntityFactory $entityFactory)
    {
        $this->entityFactory = $entityFactory;
    }
}
