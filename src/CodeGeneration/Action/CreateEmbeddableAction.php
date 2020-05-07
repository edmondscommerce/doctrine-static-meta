<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action;

// phpcs:disable
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Embeddable\FakerData\EmbeddableFakerDataCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Embeddable\Interfaces\HasEmbeddableInterfaceCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Embeddable\Interfaces\Objects\EmbeddableInterfaceCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Embeddable\Objects\EmbeddableCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Embeddable\Traits\HasEmbeddableTraitCreator;
use RuntimeException;

// phpcs:enable
class CreateEmbeddableAction implements ActionInterface
{

    /**
     * @var EmbeddableFakerDataCreator
     */
    private EmbeddableFakerDataCreator $fakerDataCreator;
    /**
     * @var EmbeddableInterfaceCreator
     */
    private EmbeddableInterfaceCreator $interfaceCreator;
    /**
     * @var HasEmbeddableInterfaceCreator
     */
    private HasEmbeddableInterfaceCreator $hasInterfaceCreator;
    /**
     * @var EmbeddableCreator
     */
    private EmbeddableCreator $embeddableCreator;
    /**
     * @var HasEmbeddableTraitCreator
     */
    private HasEmbeddableTraitCreator $hasCreator;

    /**
     * @var string|null
     */
    private ?string $catName;

    /**
     * @var string|null
     */
    private ?string $name;

    public function __construct(
        EmbeddableFakerDataCreator $fakerDataCreator,
        EmbeddableInterfaceCreator $interfaceCreator,
        HasEmbeddableInterfaceCreator $hasInterfaceCreator,
        EmbeddableCreator $embeddableCreator,
        HasEmbeddableTraitCreator $hasCreator
    ) {
        $this->fakerDataCreator    = $fakerDataCreator;
        $this->interfaceCreator    = $interfaceCreator;
        $this->hasInterfaceCreator = $hasInterfaceCreator;
        $this->embeddableCreator   = $embeddableCreator;
        $this->hasCreator          = $hasCreator;
    }


    /**
     * This must be the method that actually performs the action
     *
     * All your requirements, configuration and dependencies must be called with individual setters
     */
    public function run(): void
    {
        if ('' === (string)$this->catName) {
            throw new RuntimeException('You must call setCatName before running this action');
        }
        if ('' === (string)$this->name) {
            throw new RuntimeException('You must call setName before running this action');
        }
        $this->fakerDataCreator->setCatName($this->catName)->setName($this->name)->createTargetFileObject()->write();
        $this->interfaceCreator->setCatName($this->catName)->setName($this->name)->createTargetFileObject()->write();
        $this->hasInterfaceCreator->setCatName($this->catName)->setName($this->name)->createTargetFileObject()->write();
        $this->embeddableCreator->setCatName($this->catName)->setName($this->name)->createTargetFileObject()->write();
        $this->hasCreator->setCatName($this->catName)->setName($this->name)->createTargetFileObject()->write();
    }

    public function setProjectRootNamespace(string $projectRootNamespace)
    {
        $this->fakerDataCreator->setProjectRootNamespace($projectRootNamespace);
        $this->interfaceCreator->setProjectRootNamespace($projectRootNamespace);
        $this->hasInterfaceCreator->setProjectRootNamespace($projectRootNamespace);
        $this->embeddableCreator->setProjectRootNamespace($projectRootNamespace);
        $this->hasCreator->setProjectRootNamespace($projectRootNamespace);

        return $this;
    }

    public function setProjectRootDirectory(string $projectRootDirectory)
    {
        $this->fakerDataCreator->setProjectRootDirectory($projectRootDirectory);
        $this->interfaceCreator->setProjectRootDirectory($projectRootDirectory);
        $this->hasInterfaceCreator->setProjectRootDirectory($projectRootDirectory);
        $this->embeddableCreator->setProjectRootDirectory($projectRootDirectory);
        $this->hasCreator->setProjectRootDirectory($projectRootDirectory);

        return $this;
    }

    /**
     * @param string $catName
     *
     * @return CreateEmbeddableAction
     */
    public function setCatName(string $catName): CreateEmbeddableAction
    {
        $this->catName = $catName;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return CreateEmbeddableAction
     */
    public function setName(string $name): CreateEmbeddableAction
    {
        $this->name = $name;

        return $this;
    }
}
