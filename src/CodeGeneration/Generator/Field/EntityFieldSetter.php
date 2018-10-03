<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\DataTransferObjects\DataTransferObjectCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FileCreationTransaction;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FindAndReplaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Modification\CodeGenClassTypeFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PathHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\ReflectionHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Nette\PhpGenerator\ClassType;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Symfony\Component\Filesystem\Filesystem;

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
     * @var CodeGenClassTypeFactory
     */
    protected $codeGenClassTypeFactory;
    /**
     * @var DataTransferObjectCreator
     */
    private $dataTransferObjectCreator;

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
        DataTransferObjectCreator $dataTransferObjectCreator,
        CodeGenClassTypeFactory $codeGenClassTypeFactory
    ) {
        parent::__construct(
            $filesystem,
            $fileCreationTransaction,
            $namespaceHelper,
            $config,
            $codeHelper,
            $pathHelper,
            $findAndReplaceHelper,
            $codeGenClassTypeFactory
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
            $entityReflection          = ReflectionClass::createFromName($entityFqn);
            $entityClassType           = $this->codeGenClassTypeFactory->createClassTypeFromBetterReflection($entityReflection);
            $entityInterfaceFqn        = $this->namespaceHelper->getEntityInterfaceFromEntityFqn($entityFqn);
            $entityInterfaceReflection = ReflectionClass::createFromName($entityInterfaceFqn);
            $entityInterfaceClassType  =
                $this->codeGenClassTypeFactory->createClassTypeFromBetterReflection($entityInterfaceReflection);
            $fieldReflection           = ReflectionClass::createFromName($fieldFqn);
            $fieldClassType            = $this->codeGenClassTypeFactory->createClassTypeFromBetterReflection($fieldReflection);
            $fieldInterfaceFqn         = \str_replace(
                ['Traits', 'Trait'],
                ['Interfaces', 'Interface'],
                $fieldFqn
            );
            $fieldInterfaceReflection  = ReflectionClass::createFromName($fieldInterfaceFqn);
            $this->checkInterfaceLooksLikeField($fieldInterfaceReflection);
            $fieldInterfaceClassType =
                $this->codeGenClassTypeFactory->createClassTypeFromBetterReflection($fieldInterfaceReflection);
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Failed loading the entity or field from FQN: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
        if ($this->alreadyUsingFieldWithThisShortName($entityClassType, $fieldClassType)) {
            throw new \InvalidArgumentException(
                'Entity already has a field with the the short name: ' . $fieldClassType->getName()
                . "\n\nUse statements:" . print_r($entityClassType->getTraits(), true)
                . "\n\nProperty names:" . print_r($entityClassType->getProperties(), true)
            );
        }
        $entityClassType->addTrait($fieldFqn);
        $this->codeHelper->generate($entityClassType, $entityReflection->getFileName());
        $entityInterfaceClassType->addInterface($fieldInterfaceClassType);
        $this->codeHelper->generate($entityInterfaceClassType, $entityInterfaceReflection->getFileName());
        if ($this->fieldHasFakerProvider($fieldReflection)) {
            $this->updater->updateFakerProviderArray($this->pathToProjectRoot, $fieldFqn, $entityFqn);
        }
        $this->dataTransferObjectCreator->setNewObjectFqnFromEntityFqn($entityFqn)
                                        ->setProjectRootNamespace($this->projectRootNamespace)
                                        ->createTargetFileObject()
                                        ->write();
    }

    protected function checkInterfaceLooksLikeField(ReflectionClass $fieldInterfaceReflection): void
    {
        $lookFor = [
            'PROP_',
            'DEFAULT_',
        ];
        $found   = [];
        $consts  = $fieldInterfaceReflection->getConstants();
        foreach (\array_keys($consts) as $name) {
            foreach ($lookFor as $key => $prefix) {
                if (\ts\stringStartsWith($name, $prefix)) {
                    $found[$key] = $prefix;
                }
            }
        }
        if ($found !== $lookFor) {
            throw new \InvalidArgumentException(
                'Field ' . $fieldInterfaceReflection->getName()
                . ' does not look like a field interface, failed to find the following const prefixes: '
                . "\n" . print_r($lookFor, true)
            );
        }
    }

    protected function alreadyUsingFieldWithThisShortName(ClassType $entityClassType, ClassType $fieldClassType): bool
    {
        $traits = $entityClassType->getTraits();

        return \in_array($traits, $fieldClassType->getName(), true);
    }

    protected function fieldHasFakerProvider(\ts\Reflection\ReflectionClass $fieldReflection): bool
    {
        return \class_exists(
            $this->reflectionHelper->getFakerProviderFqnFromFieldTraitReflection($fieldReflection)
        );
    }
}
