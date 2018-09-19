<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Savers;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\AbstractCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ReplaceEntitiesSubNamespaceProcess;

class EntitySaverCreator extends AbstractCreator
{
    public const FIND_NAME = 'TemplateEntitySaver';

    public const TEMPLATE_PATH = self::ROOT_TEMPLATE_PATH . '/src/Entity/Savers/' . self::FIND_NAME . '.php';

    public function configurePipeline(): void
    {
        parent::configurePipeline();
        $this->registerReplaceEntitiesNamespaceProcess();
    }

    protected function registerReplaceEntitiesNamespaceProcess(): void
    {
        $process = new ReplaceEntitiesSubNamespaceProcess();
        $process->setEntityFqn(
            $this->entityFqn ?? $this->namespaceHelper->getEntityFqnFromEntitySaverFqn($this->newObjectFqn)
        );
        $this->pipeline->register($process);
    }

    public function setNewObjectFqnFromEntityFqn(string $entityFqn)
    {
        $this->entityFqn    = $entityFqn;
        $this->newObjectFqn = $this->namespaceHelper->getEntitySaverFqnFromEntityFqn($entityFqn);
    }
}