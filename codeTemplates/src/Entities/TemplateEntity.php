<?php declare(strict_types=1);

namespace TemplateNamespace\Entities;

// phpcs:disable
use Doctrine\DBAL\Exception\InvalidArgumentException;
use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
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

    final public function update(array $values): self
    {
        $setters = self::getDoctrineStaticMeta()->getSetters();
        foreach ($values as $property => $value) {
            $expectedSettter = 'set' . $property;
            if (isset($setters[$expectedSettter])) {
                $this->$expectedSettter($value);
                continue;
            }
            throw new InvalidArgumentException(
                'Unexpected property ' . $property . ', no setter ' . $expectedSettter
            );
        }
        $this->getValidator()->validate();

        return $this;
    }
}
