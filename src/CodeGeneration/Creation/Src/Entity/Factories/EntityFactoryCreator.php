<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Factories;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\AbstractCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ReplaceEntitiesNamespaceProcess;

class EntityFactoryCreator extends AbstractCreator
{
    public const FIND_NAME = 'TemplateEntityFactory';

    public const TEMPLATE_PATH = self::ROOT_TEMPLATE_PATH . '/src/Entity/Factories/' . self::FIND_NAME . '.php';

    public function configurePipeline(): void
    {
        parent::configurePipeline();
        $this->registerReplaceEntitiesNamespaceProcess();
    }

    protected function registerReplaceEntitiesNamespaceProcess(): void
    {
        $process = new ReplaceEntitiesNamespaceProcess();
        $process->setEntitySubNamespace(
            $this->namespaceHelper->getEntitySubNamespace(
                $this->namespaceHelper->getEntityFromEntityFactoryFqn($this->newObjectFqn)
            )
        );
        $this->pipeline->register($process);
    }
}