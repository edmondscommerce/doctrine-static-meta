<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Traits;

use EdmondsCommerce\DoctrineStaticMeta\DoctrineStaticMeta;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactoryInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\Validation\EntityDataValidatorInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ValidationException;
use Ramsey\Uuid\UuidInterface;

trait AlwaysValidTrait
{
    /**
     * @var EntityDataValidatorInterface
     */
    private $entityDataValidator;

    /**
     * This is a special property that is manipulated via Reflection in the Entity factory.
     *
     * Whilst a transaction is running, validation is suspended, and then at the end of a transaction the full
     * validation is performed
     *
     * @var bool
     */
    private $creationTransactionRunning = false;

    final public static function create(
        EntityFactoryInterface $factory,
        DataTransferObjectInterface $dto = null
    ): self {
        $entity = new static();
        $factory->initialiseEntity($entity);
        if (null !== $dto) {
            $entity->update($dto);

            return $entity;
        }
        $entity->getValidator()->validate();

        return $entity;
    }

    /**
     * Update and validate the Entity.
     *
     * The DTO can
     *  - contain data not related to this Entity, it will be ignored
     *  - not have to have all the data for this Entity, it will only update where the DTO has the setter
     *
     * The entity state after update will be validated
     *
     * Will roll back all updates if validation fails
     *
     * @param DataTransferObjectInterface $dto
     *
     * @throws ValidationException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    final public function update(DataTransferObjectInterface $dto): void
    {
        $backup  = [];
        $setters = self::getDoctrineStaticMeta()->getSetters();
        try {
            foreach ($setters as $getterName => $setterName) {
                if (false === method_exists($dto, $getterName)) {
                    continue;
                }
                $dtoValue = $dto->$getterName();
                if ($dtoValue instanceof UuidInterface && (string)$dtoValue === (string)$this->$getterName()) {
                    continue;
                }
                if (false === $this->creationTransactionRunning) {
                    $gotValue = null;
                    try {
                        $gotValue = $this->$getterName();
                    } catch (\TypeError $e) {
                        //Required items will type error on the getter as they have no value
                    }
                    if ($dtoValue === $gotValue) {
                        continue;
                    }
                    $backup[$setterName] = $gotValue;
                }

                $this->$setterName($dtoValue);
            }
            if (true === $this->creationTransactionRunning) {
                return;
            }
            $this->getValidator()->validate();
        } catch (ValidationException | \TypeError $e) {
            $reflectionClass = $this::getDoctrineStaticMeta()->getReflectionClass();
            foreach ($backup as $setterName => $backupValue) {
                /**
                 * We have to use reflection here because required property setter will not accept nulls
                 * which may be the backup value, especially on new object creation
                 */
                $propertyName       = substr($setterName, 3);
                $reflectionProperty = $reflectionClass->getProperty($propertyName);
                $reflectionProperty->setValue($this, $backupValue);
            }
            throw $e;
        }
    }

    private function getValidator(): EntityDataValidatorInterface
    {
        if (!$this->entityDataValidator instanceof EntityDataValidatorInterface) {
            throw new \RuntimeException(
                'You must call injectDataValidator before being able to update an Entity'
            );
        }

        return $this->entityDataValidator;
    }

    abstract public static function getDoctrineStaticMeta(): DoctrineStaticMeta;

    /**
     * This method is called automatically by the EntityFactory when initialisig the Entity, by way of the
     * EntityDependencyInjector
     *
     * @param EntityDataValidatorInterface $entityDataValidator
     */
    public function injectEntityDataValidator(EntityDataValidatorInterface $entityDataValidator)
    {
        $this->entityDataValidator = $entityDataValidator;
        $this->entityDataValidator->setEntity($this);
    }
}
