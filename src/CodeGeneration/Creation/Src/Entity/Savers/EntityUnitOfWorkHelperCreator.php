<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Savers;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\AbstractCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ReplaceEntitiesSubNamespaceProcess;

class EntityUnitOfWorkHelperCreator extends AbstractCreator
{
    public const FIND_NAME = 'TemplateEntityUnitOfWorkHelper';

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

    public function setNewObjectFqnFromEntityFqn(string $entityFqn): self
    {
        $this->entityFqn    = $entityFqn;
        $this->newObjectFqn = $this->namespaceHelper->getEntityUnitOfWorkHelperFqnFromEntityFqn($entityFqn);

        return $this;
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
        return $this->entityFqn ??
            $this->namespaceHelper->getEntityFqnFromEntityUnitOfWorkHelperFqn($this->newObjectFqn);
    }
}
