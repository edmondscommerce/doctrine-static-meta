<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Savers;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\AbstractCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ReplaceEntitiesSubNamespaceProcess;

class EntitySaverCreator extends AbstractCreator
{
    public const FIND_NAME = 'TemplateEntitySaver';

    public const TEMPLATE_PATH = self::ROOT_TEMPLATE_PATH . '/src/Entity/Savers/' . self::FIND_NAME . '.php';
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
        $process->setProjectRootNamespace($this->projectRootNamespace);
        $process->setEntityFqn($this->getEntityFqn());
        $this->pipeline->register($process);
    }

    private function getEntityFqn(): string
    {
        return $this->entityFqn ?? $this->namespaceHelper->getEntityFqnFromEntitySaverFqn($this->newObjectFqn);
    }

    public function setNewObjectFqnFromEntityFqn(string $entityFqn): self
    {
        $this->entityFqn    = $entityFqn;
        $this->newObjectFqn = $this->namespaceHelper->getEntitySaverFqnFromEntityFqn($entityFqn);

        return $this;
    }
}
