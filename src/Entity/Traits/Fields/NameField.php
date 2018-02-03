<?php declare(strict_types=1);


namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Traits\Fields;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

trait NameField
{
    /**
     * @var string
     */
    private $name;

    protected static function getPropertyMetaForName(ClassMetadataBuilder $builder): void
    {
        $builder->createField('name', Type::STRING)
            ->nullable(false)
            ->length(255)
            ->build();
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }
}
