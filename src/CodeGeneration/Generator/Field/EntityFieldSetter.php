<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\DataTransferObjects\DtoCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FileCreationTransaction;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FindAndReplaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PathHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\ReflectionHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Exception;
use gossi\codegen\model\PhpClass;
use gossi\codegen\model\PhpInterface;
use gossi\codegen\model\PhpTrait;
use InvalidArgumentException;
use Symfony\Component\Filesystem\Filesystem;
use ts\Reflection\ReflectionClass;
use function array_keys;
use function class_exists;
use function str_replace;

/**
 * Class EntityFieldSetter
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EntityFieldSetter extends AbstractGenerator
{
    /**
     * @var AbstractTestFakerDataProviderUpdater
     */
    protected $updater;
    /**
     * @var ReflectionHelper
     */
    protected $reflectionHelper;
    /**
     * @var DtoCreator
     */
    private $dataTransferObjectCreator;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @param Filesystem                           $filesystem
     * @param FileCreationTransaction              $fileCreationTransaction
     * @param NamespaceHelper                      $namespaceHelper
     * @param Config                               $config
     * @param CodeHelper                           $codeHelper
     * @param PathHelper                           $pathHelper
     * @param FindAndReplaceHelper                 $findAndReplaceHelper
     * @param AbstractTestFakerDataProviderUpdater $updater
     * @param ReflectionHelper                     $reflectionHelper
     * @param DtoCreator                           $dataTransferObjectCreator
     */
    public function __construct(
        Filesystem $filesystem,
        FileCreationTransaction $fileCreationTransaction,
        NamespaceHelper $namespaceHelper,
        Config $config,
        CodeHelper $codeHelper,
        PathHelper $pathHelper,
        FindAndReplaceHelper $findAndReplaceHelper,
        AbstractTestFakerDataProviderUpdater $updater,
        ReflectionHelper $reflectionHelper,
        DtoCreator $dataTransferObjectCreator
    ) {
        parent::__construct(
            $filesystem,
            $fileCreationTransaction,
            $namespaceHelper,
            $config,
            $codeHelper,
            $pathHelper,
            $findAndReplaceHelper
        );
        $this->updater                   = $updater;
        $this->reflectionHelper          = $reflectionHelper;
        $this->dataTransferObjectCreator = $dataTransferObjectCreator;
    }


    /**
     * @param string $fieldFqn
     * @param string $entityFqn
     *
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function setEntityHasField(string $entityFqn, string $fieldFqn): void
    {
        try {
            $entityReflection          = new ReflectionClass($entityFqn);
            $entity                    = PhpClass::fromFile($entityReflection->getFileName());
            $entityInterfaceFqn        = $this->namespaceHelper->getEntityInterfaceFromEntityFqn($entityFqn);
            $entityInterfaceReflection = new ReflectionClass($entityInterfaceFqn);
            $entityInterface           = PhpInterface::fromFile($entityInterfaceReflection->getFileName());
            $fieldReflection           = new ReflectionClass($fieldFqn);
            $field                     = PhpTrait::fromFile($fieldReflection->getFileName());
            $fieldInterfaceFqn         = str_replace(
                ['Traits', 'Trait'],
                ['Interfaces', 'Interface'],
                $fieldFqn
            );
            $fieldInterfaceReflection  = new ReflectionClass($fieldInterfaceFqn);
            $this->checkInterfaceLooksLikeField($fieldInterfaceReflection);
            $fieldInterface = PhpInterface::fromFile($fieldInterfaceReflection->getFileName());
        } catch (Exception $e) {
            throw new DoctrineStaticMetaException(
                'Failed loading the entity or field from FQN: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
        if ($this->alreadyUsingFieldWithThisShortName($entity, $field)) {
            throw new InvalidArgumentException(
                'Entity already has a field with the the short name: ' . $field->getName()
                . "\n\nUse statements:" . print_r($entity->getUseStatements(), true)
                . "\n\nProperty names:" . print_r($entity->getPropertyNames(), true)
            );
        }
        $entity->addTrait($field);
        $this->codeHelper->generate($entity, $entityReflection->getFileName());
        $entityInterface->addInterface($fieldInterface);
        $this->codeHelper->generate($entityInterface, $entityInterfaceReflection->getFileName());
        if ($this->fieldHasFakerProvider($fieldReflection)) {
            $this->updater->updateFakerProviderArrayWithFieldFakerData($this->pathToProjectRoot, $fieldFqn, $entityFqn);
        }
        $this->dataTransferObjectCreator->setNewObjectFqnFromEntityFqn($entityFqn)
                                        ->setProjectRootDirectory($this->pathToProjectRoot)
                                        ->setProjectRootNamespace($this->projectRootNamespace)
                                        ->createTargetFileObject()
                                        ->write();
    }

    /**
     * @param ReflectionClass $fieldInterfaceReflection
     */
    protected function checkInterfaceLooksLikeField(ReflectionClass $fieldInterfaceReflection): void
    {
        $lookFor = [
            'PROP_',
            'DEFAULT_',
        ];
        $found   = [];
        $consts  = $fieldInterfaceReflection->getConstants();
        foreach (array_keys($consts) as $name) {
            foreach ($lookFor as $key => $prefix) {
                if (\ts\stringStartsWith($name, $prefix)) {
                    $found[$key] = $prefix;
                }
            }
        }
        if ($found !== $lookFor) {
            throw new InvalidArgumentException(
                'Field ' . $fieldInterfaceReflection->getName()
                . ' does not look like a field interface, failed to find the following const prefixes: '
                . "\n" . print_r($lookFor, true)
            );
        }
    }

    protected function alreadyUsingFieldWithThisShortName(PhpClass $entity, PhpTrait $field): bool
    {
        $useStatements = $entity->getUseStatements();

        return null !== $useStatements->get($field->getName());
    }

    protected function fieldHasFakerProvider(ReflectionClass $fieldReflection): bool
    {
        return class_exists(
            $this->reflectionHelper->getFakerProviderFqnFromFieldTraitReflection($fieldReflection)
        );
    }
}
