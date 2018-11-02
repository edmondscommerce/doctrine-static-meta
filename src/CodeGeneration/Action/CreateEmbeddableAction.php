<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Embeddable\FakerData\EmbeddableFakerDataCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Embeddable\Interfaces\HasEmbeddableInterfaceCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Embeddable\Interfaces\Objects\EmbeddableInterfaceCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Embeddable\Objects\EmbeddableCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Embeddable\Traits\HasEmbeddableCreator;

class CreateEmbeddableAction implements ActionInterface
{

    /**
     * @var EmbeddableFakerDataCreator
     */
    private $fakerDataCreator;
    /**
     * @var EmbeddableInterfaceCreator
     */
    private $interfaceCreator;
    /**
     * @var HasEmbeddableInterfaceCreator
     */
    private $hasInterfaceCreator;
    /**
     * @var EmbeddableCreator
     */
    private $embeddableCreator;
    /**
     * @var HasEmbeddableCreator
     */
    private $hasCreator;

    /**
     * @var string
     */
    private $catName;

    /**
     * @var string
     */
    private $name;

    public function __construct(
        EmbeddableFakerDataCreator $fakerDataCreator,
        EmbeddableInterfaceCreator $interfaceCreator,
        HasEmbeddableInterfaceCreator $hasInterfaceCreator,
        EmbeddableCreator $embeddableCreator,
        HasEmbeddableCreator $hasCreator
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
        // TODO: Implement run() method.
    }

    public function setProjectRootNamespace(string $projectRootNamespace)
    {
        $this->fakerDataCreator->setProjectRootNamespace($projectRootNamespace);
        $this->interfaceCreator->setProjectRootNamespace($projectRootNamespace);
        $this->hasInterfaceCreator->setProjectRootNamespace($projectRootNamespace);
        $this->embeddableCreator->setProjectRootNamespace($projectRootNamespace);
        $this->hasCreator->setProjectRootNamespace($projectRootNamespace);
    }

    public function setProjectRootDirectory(string $projectRootDirectory)
    {
        $this->fakerDataCreator->setProjectRootDirectory($projectRootDirectory);
        $this->interfaceCreator->setProjectRootDirectory($projectRootDirectory);
        $this->hasInterfaceCreator->setProjectRootDirectory($projectRootDirectory);
        $this->embeddableCreator->setProjectRootDirectory($projectRootDirectory);
        $this->hasCreator->setProjectRootDirectory($projectRootDirectory);
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