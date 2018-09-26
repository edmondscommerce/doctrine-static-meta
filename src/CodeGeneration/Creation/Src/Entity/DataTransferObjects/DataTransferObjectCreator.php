<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\DataTransferObjects;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\AbstractCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ReplaceEntitiesSubNamespaceProcess;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\Src\Entity\CreateDataTransferObjectBodyProcess;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\ReflectionHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;

class DataTransferObjectCreator extends AbstractCreator
{
    public const FIND_NAME = 'TemplateEntityDto';

    public const TEMPLATE_PATH = self::ROOT_TEMPLATE_PATH .
                                 '/src/Entity/DataTransferObjects/' .
                                 self::FIND_NAME .
                                 '.php';
    /**
     * @var ReflectionHelper
     */
    private $reflectionHelper;
    /**
     * @var string
     */
    private $entityFqn;

    public function __construct(
        FileFactory $fileFactory,
        NamespaceHelper $namespaceHelper,
        File\Writer $fileWriter,
        Config $config,
        FindReplaceFactory $findReplaceFactory,
        ReflectionHelper $reflectionHelper
    ) {
        parent::__construct($fileFactory, $namespaceHelper, $fileWriter, $config, $findReplaceFactory);
        $this->reflectionHelper = $reflectionHelper;
    }

    public function configurePipeline(): void
    {
        parent::configurePipeline();
        $this->registerReplaceEntitiesNamespaceProcess();
        $this->registerDataTransferObjectProcess();
    }

    protected function registerReplaceEntitiesNamespaceProcess(): void
    {
        $process = new ReplaceEntitiesSubNamespaceProcess();
        $process->setEntityFqn($this->getEntityFqn());
        $this->pipeline->register($process);
    }

    private function getEntityFqn(): string
    {
        return $this->entityFqn ?? $this->namespaceHelper->getEntityFqnFromEntityDtoFqn($this->newObjectFqn);
    }

    private function registerDataTransferObjectProcess(): void
    {
        $process = new CreateDataTransferObjectBodyProcess($this->reflectionHelper);
        $process->setEntityFqn($this->getEntityFqn());
        $this->pipeline->register($process);
    }

    public function setNewObjectFqnFromEntityFqn(string $entityFqn): self
    {
        $this->entityFqn    = $entityFqn;
        $this->newObjectFqn = $this->namespaceHelper->getEntityDtoFqnFromEntityFqn($entityFqn);

        return $this;
    }
}