<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Fields\Interfaces\FieldInterfaceCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Fields\Traits\FieldTraitCreator;

class CreateDbalFieldAndInterfaceAction implements ActionInterface
{
    /**
     * @var FieldTraitCreator
     */
    private $fieldTraitCreator;
    /**
     * @var FieldInterfaceCreator
     */
    private $fieldInterfaceCreator;

    public function __construct(FieldTraitCreator $fieldTraitCreator, FieldInterfaceCreator $fieldInterfaceCreator)
    {
        $this->fieldTraitCreator     = $fieldTraitCreator;
        $this->fieldInterfaceCreator = $fieldInterfaceCreator;
    }

    /**
     * This must be the method that actually performs the action
     *
     * All your requirements, configuration and dependencies must be called with individual setters
     */
    public function run(): void
    {
        $this->fieldTraitCreator->createTargetFileObject()->write();
        $this->fieldInterfaceCreator->createTargetFileObject()->write();
    }

    public function setProjectRootNamespace(string $projectRootNamespace): self
    {
        $this->fieldTraitCreator->setProjectRootNamespace($projectRootNamespace);
        $this->fieldInterfaceCreator->setProjectRootNamespace($projectRootNamespace);

        return $this;
    }

    public function setProjectRootDirectory(string $projectRootDirectory): self
    {
        $this->fieldTraitCreator->setProjectRootDirectory($projectRootDirectory);
        $this->fieldInterfaceCreator->setProjectRootDirectory($projectRootDirectory);

        return $this;
    }

    public function setIsUnique(bool $isUnique): self
    {
        $this->fieldTraitCreator->setUnique($isUnique);

        return $this;
    }

    public function setDefaultValue($defaultValue): self
    {
        $this->fieldInterfaceCreator->setDefaultValue($defaultValue);

        return $this;
    }

    public function setMappingHelperCommonType(string $mappingHelperCommonType): self
    {
        $this->fieldTraitCreator->setMappingHelperCommonType($mappingHelperCommonType);
        $this->fieldInterfaceCreator->setMappingHelperCommonType($mappingHelperCommonType);

        return $this;
    }

    public function setFieldTraitFqn(string $fieldTraitFqn): self
    {
        $this->fieldTraitCreator->setNewObjectFqn($fieldTraitFqn);
        $interfaceFqn = str_replace(
            [
                '\\Traits\\',
                FieldTraitCreator::SUFFIX,
            ],
            [
                '\\Interfaces\\',
                FieldTraitCreator::INTERFACE_SUFFIX,
            ],
            $fieldTraitFqn
        );
        $this->fieldInterfaceCreator->setNewObjectFqn($interfaceFqn);

        return $this;
    }
}
