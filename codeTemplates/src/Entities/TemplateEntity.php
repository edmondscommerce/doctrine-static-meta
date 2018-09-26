<?php declare(strict_types=1);

namespace TemplateNamespace\Entities;

// phpcs:disable
use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ValidationException;
use TemplateNamespace\Entity\DataTransferObjects\TemplateEntityDto;
use TemplateNamespace\Entity\Interfaces\TemplateEntityInterface;

class TemplateEntity implements TemplateEntityInterface
{
    // phpcs:enable
    use DSM\Traits\UsesPHPMetaDataTrait;

    use DSM\Traits\ValidatedEntityTrait;

    use DSM\Traits\ImplementNotifyChangeTrackingPolicy;

    use DSM\Fields\Traits\PrimaryKey\IdFieldTrait;


    final private function __construct()
    {
        $this->runInitMethods();
    }

    final public static function create(DSM\Factory\EntityFactory $factory, array $values): self
    {
        $entity = new static();
        $factory->initialiseEntity($entity);
        $entity->update($values);

        return $entity;
    }

    /**
     * Update and validate the Entity.
     *
     * Will roll back all updates if validation fails
     *
     * @param DataTransferObjectInterface $dto
     *
     * @return TemplateEntity
     * @throws ValidationException
     * @throws \ReflectionException
     */
    final public function update(DSM\Interfaces\DataTransferObjectInterface $dto): self
    {
        $setters   = self::getDoctrineStaticMeta()->getSetters();
        $backupDto = $this->getDto();
        foreach ($setters as $getterName => $setterName) {
            $this->$setterName($dto->$getterName());
        }
        try {
            $this->getValidator()->validate();
        } catch (ValidationException $e) {
            foreach ($setters as $getterName => $setterName) {
                $this->$setterName($backupDto->$getterName());
            }
            throw $e;
        }

        return $this;
    }

    final public function getDto(): DSM\Interfaces\DataTransferObjectInterface
    {
        $setters = self::getDoctrineStaticMeta()->getSetters();
        $dto     = new TemplateEntityDto();
        foreach ($setters as $getterName => $setterName) {
            $dto->$setterName($this->$getterName());
        }

        return $dto;
    }
}
