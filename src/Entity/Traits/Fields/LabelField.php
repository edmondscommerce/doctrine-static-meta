<?php declare(strict_types=1);


namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Traits\Fields;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

trait LabelField
{
    /**
     * @var string
     */
    private $label;

    protected static function getPropertyMetaForLabel(ClassMetadataBuilder $builder): void
    {
        $builder->createField('label', Type::STRING)
            ->nullable(false)
            ->length(255)
            ->build();
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Set label
     *
     * @param string $label
     *
     * @return $this
     */
    public function setLabel(string $label)
    {
        $this->label = $label;

        return $this;
    }
}
