<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\DoctrineStaticMeta;

class RequiredRelation
{
    /** @var string */
    private $propertyName;
    /** @var string */
    private $relationEntityFqn;
    /** @var bool */
    private $pluralRelation;

    /**
     * @param string $propertyName
     * @param string $relationEntityFqn
     * @param bool   $pluralRelation
     */
    public function __construct(string $propertyName, string $relationEntityFqn, bool $pluralRelation)
    {
        $this->propertyName      = $propertyName;
        $this->relationEntityFqn = $relationEntityFqn;
        $this->pluralRelation    = $pluralRelation;
    }

    /**
     * @return string
     */
    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    /**
     * @return string
     */
    public function getRelationEntityFqn(): string
    {
        return $this->relationEntityFqn;
    }

    /**
     * @return bool
     */
    public function isPluralRelation(): bool
    {
        return $this->pluralRelation;
    }

}
