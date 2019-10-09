<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Factories;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\AbstractCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ReplaceEntitiesSubNamespaceProcess;

class EntityDtoFactoryCreator extends AbstractCreator
{
    public const FIND_NAME = 'TemplateEntityDtoFactory';

    public const TEMPLATE_PATH = self::ROOT_TEMPLATE_PATH . '/src/Entity/Factories/' . self::FIND_NAME . '.php';
    /**
     * @var string
     */
    private $entityFqn;

    public function configurePipeline(): void
    {
        parent::configurePipeline();
        $this->registerReplaceEntitiesNamespaceProcess();
        $this->registerEntityReplaceName($this->getEntityFqn());
    }

    protected function registerReplaceEntitiesNamespaceProcess(): void
    {
        $process = new ReplaceEntitiesSubNamespaceProcess();
        $process->setEntityFqn($this->getEntityFqn());
        $process->setProjectRootNamespace($this->projectRootNamespace);
        $this->pipeline->register($process);
    }

    private function getEntityFqn(): string
    {
        return $this->entityFqn ?? $this->namespaceHelper->getEntityFqnFromEntityDtoFactoryFqn($this->newObjectFqn);
    }

    public function setNewObjectFqnFromEntityFqn(string $entityFqn): self
    {
        $this->entityFqn    = $entityFqn;
        $this->newObjectFqn = $this->namespaceHelper->getDtoFactoryFqnFromEntityFqn($entityFqn);

        return $this;
    }
}
