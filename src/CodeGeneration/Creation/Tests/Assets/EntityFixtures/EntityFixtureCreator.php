<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Tests\Assets\EntityFixtures;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\AbstractCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ReplaceEntitiesSubNamespaceProcess;

class EntityFixtureCreator extends AbstractCreator
{
    public const FIND_NAME = 'TemplateEntityFixture';

    public const TEMPLATE_PATH = self::ROOT_TEMPLATE_PATH . 'tests/Assets/EntityFixtures/' . self::FIND_NAME . '.php';

    /**
     * @var string
     */
    private $entityFqn;

    public function configurePipeline(): void
    {
        parent::configurePipeline();
        $this->registerReplaceEntitiesNamespaceProcess();
    }

    protected function registerReplaceEntitiesNamespaceProcess(): void
    {
        $process = new ReplaceEntitiesSubNamespaceProcess();
        $process->setEntityFqn(
            $this->entityFqn ?? $this->namespaceHelper->getEntityFqnFromFixtureFqn($this->newObjectFqn)
        );
        $this->pipeline->register($process);
    }

    public function setNewObjectFqnFromEntityFqn(string $entityFqn)
    {
        $this->entityFqn    = $entityFqn;
        $this->newObjectFqn = $this->namespaceHelper->getFixtureFqnFromEntityFqn($entityFqn);
    }
}