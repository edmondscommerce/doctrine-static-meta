<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Savers;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use Ramsey\Uuid\UuidInterface;
use TemplateNamespace\Entities\TemplateEntity;
use TemplateNamespace\Entity\DataTransferObjects\TemplateEntityDto;
use TemplateNamespace\Entity\Interfaces\TemplateEntityInterface;

class TemplateEntityUnitOfWorkHelper
{
    public const ENTITY_FQN = TemplateEntity::class;

    /**
     * @var UnitOfWork
     */
    private $unitOfWork;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->unitOfWork = $entityManager->getUnitOfWork();
    }

    public function getEntityFromUnitOfWorkUsingDto(
        TemplateEntityDto $dto
    ): TemplateEntityInterface {
        $uuid = $dto->getId();
        if (false === ($uuid instanceof UuidInterface)) {
            throw new \RuntimeException('Unsupported ID type:' . print_r($uuid, true));
        }
        if ($this->hasEntityByUuid($uuid)) {
            return $this->getEntityByUuid($uuid);
        }
        throw new \RuntimeException('Failed getting Entity from Unit of Work for ID ' . (string)$uuid);
    }

    public function hasEntityByUuid(
        UuidInterface $uuid
    ): bool {
        $map = $this->unitOfWork->getIdentityMap();

        return isset($map[self::ENTITY_FQN][(string)$uuid]);
    }

    public function getEntityByUuid(
        UuidInterface $uuid
    ): TemplateEntityInterface {
        $map        = $this->getIdentityMapForEntity();
        $uuidString = (string)$uuid;
        if (isset($map[$uuidString]) && ($map[$uuidString] instanceof TemplateEntityInterface)) {
            return $map[$uuidString];
        }
        throw new \RuntimeException('Failed finding Entity in Unit of Work');
    }

    public function getIdentityMapForEntity(): array
    {
        $map = $this->unitOfWork->getIdentityMap();
        if (false === isset($map[self::ENTITY_FQN])) {
            throw new \RuntimeException('No Identities in the Unit of Work for this Entity FQN');
        }

        return $map[self::ENTITY_FQN];
    }

    public function hasRecordOfDto(
        TemplateEntityDto $dto
    ): bool {
        return $this->hasEntityByUuid($dto->getId());
    }

}
