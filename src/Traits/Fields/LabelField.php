<?php declare(strict_types=1);


namespace Edmonds\DoctrineStaticMeta\Traits\Fields;


use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

trait LabelField
{
    /**
     * @var string
     */
    private $label;

    protected static function getPropertyMetaForLabel(ClassMetadataBuilder $builder)
    {
        $builder->createField('label', Type::STRING)
            ->nullable(false)
            ->length(255);
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
