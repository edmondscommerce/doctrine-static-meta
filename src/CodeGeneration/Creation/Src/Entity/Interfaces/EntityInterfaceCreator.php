<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\AbstractCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ReplaceEntitiesSubNamespaceProcess;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\Src\Entity\Interfaces\AddSettableUuidInterfaceProcess;

class EntityInterfaceCreator extends AbstractCreator
{
    public const FIND_NAME = 'TemplateEntityInterface';

    public const TEMPLATE_PATH = self::ROOT_TEMPLATE_PATH . '/src/Entity/Interfaces/' . self::FIND_NAME . '.php';
    /**
     * @var string
     */
    private $entityFqn;

    /**
     * @var bool
     */
    private $isSettableUuid = true;

    public function configurePipeline(): void
    {
        parent::configurePipeline();
        $this->registerReplaceEntitiesNamespaceProcess();
        if (true === $this->isSettableUuid) {
            $this->registerAddSettableUuidInterfaceProcess();
        }
    }

    protected function registerReplaceEntitiesNamespaceProcess(): void
    {
        $process = new ReplaceEntitiesSubNamespaceProcess();
        $process->setEntityFqn(
            $this->entityFqn ?? $this->namespaceHelper->getEntityFqnFromEntityInterfaceFqn($this->newObjectFqn)
        );
        $process->setProjectRootNamespace($this->projectRootNamespace);
        $this->pipeline->register($process);
    }

    protected function registerAddSettableUuidInterfaceProcess(): void
    {
        $process = new AddSettableUuidInterfaceProcess();
        $this->pipeline->register($process);
    }

    public function setNewObjectFqnFromEntityFqn(string $entityFqn): self
    {
        $this->entityFqn    = $entityFqn;
        $this->newObjectFqn = $this->namespaceHelper->getEntityInterfaceFromEntityFqn($entityFqn);

        return $this;
    }

    /**
     * @param bool $isSettableUuid
     *
     * @return EntityInterfaceCreator
     */
    public function setIsSettableUuid(bool $isSettableUuid): self
    {
        $this->isSettableUuid = $isSettableUuid;

        return $this;
    }
}
