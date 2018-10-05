<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Tests\Assets\Entity\Fixtures;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\AbstractCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ReplaceEntitiesSubNamespaceProcess;

class EntityFixtureCreator extends AbstractCreator
{
    public const FIND_NAME = 'TemplateEntityFixture';

    public const TEMPLATE_PATH = self::ROOT_TEMPLATE_PATH . 'tests/Assets/Entity/Fixtures/' . self::FIND_NAME . '.php';

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

    protected function getEntityFqn()
    {
        return $this->entityFqn ?? $this->namespaceHelper->getEntityFqnFromFixtureFqn($this->newObjectFqn);
    }

    public function setNewObjectFqnFromEntityFqn(string $entityFqn): self
    {
        $this->entityFqn    = $entityFqn;
        $this->newObjectFqn = $this->namespaceHelper->getFixtureFqnFromEntityFqn($entityFqn);

        return $this;
    }
}
