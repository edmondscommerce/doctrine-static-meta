<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Factories\UuidFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * Extend from this class when making small anonymous DTO classes
 *
 * This version will generate a non binary Uuid and should be used for Entities that implement
 * \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey\NonBinaryUuidFieldTrait
 */
abstract class AbstractAnonymousNonBinaryUuidDto implements DataTransferObjectInterface
{
    /**
     * @var string
     */
    private static $entityFqn;
    /**
     * @var UuidInterface
     */
    private $id;

    public function __construct(string $entityFqn, UuidFactory $idFactory)
    {
        self::$entityFqn = $entityFqn;
        $this->id        = $idFactory->getUuid();
    }

    public static function getEntityFqn(): string
    {
        return self::$entityFqn;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }
}
