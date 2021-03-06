<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\DataTransferObjects\DtoCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use Symfony\Component\Finder\Finder;

use function str_replace;
use function strlen;
use function strpos;
use function substr;

class CreateDtosForAllEntitiesAction implements ActionInterface
{
    /**
     * @var DtoCreator
     */
    private $dataTransferObjectCreator;
    /**
     * @var string
     */
    private $projectRootNamespace;
    /**
     * @var string
     */
    private $projectRootDirectory;
    /**
     * @var NamespaceHelper
     */
    private $namespaceHelper;

    public function __construct(DtoCreator $dataTransferObjectCreator, NamespaceHelper $namespaceHelper)
    {
        $this->dataTransferObjectCreator = $dataTransferObjectCreator;
        $this->namespaceHelper           = $namespaceHelper;
    }

    public function run(): void
    {
        $entityFqns = $this->findAllEntityFqns();
        foreach ($entityFqns as $entityFqn) {
            $this->dataTransferObjectCreator->setNewObjectFqnFromEntityFqn($entityFqn)
                                            ->createTargetFileObject()
                                            ->write();
        }
    }

    private function findAllEntityFqns(): array
    {
        $finder = new Finder();
        $finder->files()->in($this->projectRootDirectory . '/src/Entities')->name('*.php');
        $entityFqns = [];
        foreach ($finder as $splFileInfo) {
            $path         = $splFileInfo->getRealPath();
            $subPath      = substr($path, strpos($path, '/src/Entities/') + strlen('/src/Entities/'));
            $subPath      = str_replace('.php', '', $subPath);
            $entityFqns[] = $this->namespaceHelper->tidy(
                $this->projectRootNamespace . '\\Entities\\' . str_replace('/', '\\', $subPath)
            );
        }

        return $entityFqns;
    }

    public function setProjectRootNamespace(string $projectRootNamespace): self
    {
        $this->projectRootNamespace = $projectRootNamespace;
        $this->dataTransferObjectCreator->setProjectRootNamespace($projectRootNamespace);

        return $this;
    }

    public function setProjectRootDirectory(string $projectRootDirectory): self
    {
        $this->projectRootDirectory = $projectRootDirectory;
        $this->dataTransferObjectCreator->setProjectRootDirectory($projectRootDirectory);

        return $this;
    }
}
