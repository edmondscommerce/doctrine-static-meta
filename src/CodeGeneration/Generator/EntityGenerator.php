<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action\CreateEntityAction;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey\IdFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey\IntegerIdFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey\NonBinaryUuidFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey\UuidFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Exception;
use LogicException;
use RuntimeException;

use function realpath;

/**
 * Class EntityGenerator
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EntityGenerator
{

    /**
     * @var CreateEntityAction
     */
    private CreateEntityAction $action;

    public function __construct(
        CreateEntityAction $action,
        NamespaceHelper $namespaceHelper,
        Config $config
    ) {
        $this->action = $action;
        $this->setProjectRootNamespace($namespaceHelper->getProjectRootNamespaceFromComposerJson());
        $this->setPathToProjectRoot($config::getProjectRootDirectory());
    }

    /**
     * @param string $projectRootNamespace
     *
     * @return $this
     */
    public function setProjectRootNamespace(string $projectRootNamespace): self
    {
        $this->action->setProjectRootNamespace($projectRootNamespace);

        return $this;
    }

    /**
     * @param string $pathToProjectRoot
     *
     * @return $this
     * @throws RuntimeException
     */
    public function setPathToProjectRoot(string $pathToProjectRoot): self
    {
        $realPath = realpath($pathToProjectRoot);
        if (false === $realPath) {
            throw new RuntimeException('Invalid path to project root ' . $pathToProjectRoot);
        }
        $this->action->setProjectRootDirectory($realPath);

        return $this;
    }

    /**
     * @param string $entityFqn
     * @param bool   $generateSpecificEntitySaver
     *
     * @return string absolute path to created entity file
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function generateEntity(
        string $entityFqn,
        bool $generateSpecificEntitySaver = false
    ): string {
        try {
            $this->action->setEntityFqn($entityFqn);
            $this->action->setGenerateSaver($generateSpecificEntitySaver);
            $this->action->run();

            return $this->action->getCreatedEntityFilePath();
        } catch (Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in ' . __METHOD__ . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @param int $idType
     *
     * @return EntityGenerator
     * @deprecated
     * @see setPrimaryKeyFieldTrait
     */
    public function setPrimaryKeyType(int $idType): self
    {
        switch ($idType) {
            case 1:
                return $this->setPrimaryKeyFieldTrait(IdFieldTrait::class);
            case 4:
                return $this->setPrimaryKeyFieldTrait(NonBinaryUuidFieldTrait::class);
            case 8:
                return $this->setPrimaryKeyFieldTrait(UuidFieldTrait::class);
            default:
                throw new LogicException('Unknown trait selected');
        }
    }

    public function setPrimaryKeyFieldTrait(string $primaryKeyTraitFqn): self
    {
        $this->action->setPrimaryKeyTraitFqn($primaryKeyTraitFqn);

        return $this;
    }
}
