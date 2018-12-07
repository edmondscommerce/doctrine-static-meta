<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Factories;

// phpcs:disable -- line length
use EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\DtoFactory;
use TemplateNamespace\Entities\TemplateEntity;
use TemplateNamespace\Entity\DataTransferObjects\TemplateEntityDto;
use TemplateNamespace\Entity\Interfaces\TemplateEntityInterface;

// phpcs: enable
class TemplateEntityDtoFactory
{
    /**
     * @var DtoFactory
     */
    private $dtoFactory;

    public function __construct(DtoFactory $dtoFactory)
    {
        $this->dtoFactory = $dtoFactory;
    }

    public function create(): TemplateEntityDto
    {
        return $this->dtoFactory->createEmptyDtoFromEntityFqn(TemplateEntity::class);
    }

    public function createDtoFromTemplateEntity(TemplateEntityInterface $entity): TemplateEntityDto
    {
        if (false === ($entity instanceof TemplateEntity)) {
            throw new \InvalidArgumentException(
                'Invalid Entity: expecting instance of ' . TemplateEntity::class
                . ', got ' . \get_class($entity));
        }

        return $this->dtoFactory->createDtoFromEntity($entity);

    }

}
