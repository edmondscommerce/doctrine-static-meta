<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * Extend from this class when making small anonymous DTO classes
 *
 * Take extra care to ensure you pass in the correct type of Uuid for your Entity or you will have problems saving to
 * the DB
 */
abstract class AbstractAnonymousDto implements DataTransferObjectInterface
{
    /**
     * @var string
     */
    private static $entityFqn;
    /**
     * @var UuidInterface
     */
    private $id;

    public function __construct(string $entityFqn, UuidInterface $id)
    {
        self::$entityFqn = $entityFqn;
        $this->id        = $id;
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