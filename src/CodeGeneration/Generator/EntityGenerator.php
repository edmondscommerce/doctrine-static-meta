<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action\CreateEntityAction;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey\IdFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey\IntegerIdFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey\NonBinaryUuidFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey\UuidFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\AbstractEntitySpecificSaver;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use gossi\codegen\model\PhpClass;
use gossi\codegen\model\PhpInterface;

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
    private $action;

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
    public function setProjectRootNamespace(string $projectRootNamespace): AbstractGenerator
    {
        $this->action->setProjectRootNamespace($this->projectRootNamespace);

        return $this;
    }

    /**
     * @param string $pathToProjectRoot
     *
     * @return $this
     * @throws \RuntimeException
     */
    public function setPathToProjectRoot(string $pathToProjectRoot): AbstractGenerator
    {
        $realPath = \realpath($pathToProjectRoot);
        if (false === $realPath) {
            throw new \RuntimeException('Invalid path to project root ' . $pathToProjectRoot);
        }
        $this->action->setProjectRootDirectory($realPath);

        return $this;
    }

    public function generateEntity(
        string $entityFqn,
        bool $generateSpecificEntitySaver = false
    ): string {
        try {
            $this->action->setEntityFqn($entityFqn);
            $this->action->run();
            if (true === $generateSpecificEntitySaver) {
                $this->createEntitySaver($entityFqn);
            }
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in ' . __METHOD__ . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Create an entity saver
     *
     * @param string $entityFqn
     *
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function createEntitySaver(string $entityFqn): void
    {
        $entitySaverFqn = \str_replace(
                              '\\' . AbstractGenerator::ENTITIES_FOLDER_NAME . '\\',
                              AbstractGenerator::ENTITY_SAVERS_NAMESPACE . '\\',
                              $entityFqn
                          ) . 'Saver';


        $entitySaver = new PhpClass();
        $entitySaver
            ->setQualifiedName($entitySaverFqn)
            ->setParentClassName('\\' . AbstractEntitySpecificSaver::class)
            ->setInterfaces(
                [
                    PhpInterface::fromFile(__DIR__ . '/../../Entity/Savers/EntitySaverInterface.php'),
                ]
            );

        list($className, , $subDirectories) = $this->parseFullyQualifiedName(
            $entitySaverFqn,
            $this->srcSubFolderName
        );

        $filePath = $this->createSubDirectoriesAndGetPath($subDirectories);

        $this->codeHelper->generate($entitySaver, $filePath . '/' . $className . '.php');
    }

    /**
     * @param int $idType
     *
     * @return EntityGenerator
     * @deprecated
     */
    public function setPrimaryKeyType(int $idType): self
    {
        switch ($idType) {
            case 1:
                return $this->setPrimaryKeyFieldTrait(IdFieldTrait::class);
            case 2:
                return $this->setPrimaryKeyFieldTrait(IntegerIdFieldTrait::class);
            case 4:
                return $this->setPrimaryKeyFieldTrait(NonBinaryUuidFieldTrait::class);
            case 8:
                return $this->setPrimaryKeyFieldTrait(UuidFieldTrait::class);
            default:
                throw new \LogicException('Unknown trait selected');
        }
    }

    public function setPrimaryKeyFieldTrait(string $primaryKeyTraitFqn): self
    {
        $this->action->setPrimaryKeyTraitFqn($primaryKeyTraitFqn);

        return $this;
    }
}
