<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Embeddable;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\AbstractCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ReplaceNameProcess;
use InvalidArgumentException;
use RuntimeException;

abstract class AbstractEmbeddableCreator extends AbstractCreator
{
    /**
     * @var string|null
     */
    protected $catName;

    /**
     * @var string|null
     */
    protected $name;


    public function createTargetFileObject(string $newObjectFqn = null): AbstractCreator
    {
        if (null !== $newObjectFqn) {
            throw new InvalidArgumentException('You do not pass a new object FQN into this creator');
        }
        if ('' === (string)$this->catName) {
            throw new RuntimeException('You must call setCatName before running this creator');
        }
        if ('' === (string)$this->name) {
            throw new RuntimeException('You must call setName before running this creator');
        }
        if ('' === (string)$this->projectRootNamespace) {
            throw new RuntimeException('You must call setProjectRootNamespace before running this creator');
        }

        return parent::createTargetFileObject($this->getNewObjectFqn());
    }

    abstract protected function getNewObjectFqn(): string;

    /**
     * @param string $catName
     *
     * @return AbstractEmbeddableCreator
     */
    public function setCatName(string $catName): AbstractEmbeddableCreator
    {
        $this->catName = $catName;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return AbstractEmbeddableCreator
     */
    public function setName(string $name): AbstractEmbeddableCreator
    {
        $this->name = $name;

        return $this;
    }

    protected function configurePipeline(): void
    {
        parent::configurePipeline();
        $this->registerReplaceSkeletonEmbeddable();
    }

    private function registerReplaceSkeletonEmbeddable(): void
    {
        $replaceName = new ReplaceNameProcess();
        $replaceName->setArgs(
            'CatName',
            $this->catName
        );
        $this->pipeline->register($replaceName);
        $replaceName = new ReplaceNameProcess();
        $replaceName->setArgs(
            'Skeleton',
            $this->name
        );
        $this->pipeline->register($replaceName);
    }
}
