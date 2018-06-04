<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PathHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\TypeHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Attribute\IpAddressFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Attribute\LabelFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Attribute\NameFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Attribute\QtyFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Date\ActionedDateFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Date\ActivatedDateFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Date\CompletedDateFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Date\DeactivatedDateFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Date\TimestampFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Flag\ApprovedFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Flag\DefaultFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Person\EmailFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Person\YearOfBirthFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use gossi\codegen\model\PhpMethod;
use gossi\codegen\model\PhpParameter;
use gossi\codegen\model\PhpTrait;
use gossi\docblock\Docblock;
use gossi\docblock\tags\UnknownTag;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class FieldGenerator
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FieldGenerator extends AbstractGenerator
{
    public const FIELD_TRAIT_SUFFIX = 'FieldTrait';

    /**
     * @var string
     */
    protected $fieldsPath;

    /**
     * @var string
     */
    protected $fieldsInterfacePath;

    /**
     * @var string
     * CamelCase Version of Field Name
     */
    protected $classy;

    /**
     * @var string
     * UPPER_SNAKE_CASE version of Field Name
     */
    protected $consty;

    /**
     * @var string
     */
    protected $phpType;

    /**
     * @var string
     */
    protected $fieldType;

    /**
     * Are we currently generating an archetype based field?
     *
     * @var bool
     */
    protected $isArchetype = false;

    /**
     * @var bool
     */
    protected $isNullable;

    /**
     * @var bool
     */
    protected $isUnique;

    /**
     * @var mixed
     */
    protected $defaultValue;

    /**
     * @var string
     */
    protected $traitNamespace;

    /**
     * @var string
     */
    protected $interfaceNamespace;

    public const STANDARD_FIELDS = [
        // Attribute
        IpAddressFieldTrait::class,
        LabelFieldTrait::class,
        NameFieldTrait::class,
        QtyFieldTrait::class,
        // Date
        ActionedDateFieldTrait::class,
        ActivatedDateFieldTrait::class,
        CompletedDateFieldTrait::class,
        DeactivatedDateFieldTrait::class,
        TimestampFieldTrait::class,
        // Flag
        ApprovedFieldTrait::class,
        DefaultFieldTrait::class,
        // Person
        EmailFieldTrait::class,
        YearOfBirthFieldTrait::class,
    ];
    /**
     * @var TypeHelper
     */
    protected $typeHelper;
    /**
     * @var ArchetypeFieldGenerator
     */
    protected $archetypeFieldCopier;
    /**
     * @var string
     */
    protected $fieldFqn;

    public function __construct(
        Filesystem $filesystem,
        FileCreationTransaction $fileCreationTransaction,
        NamespaceHelper $namespaceHelper,
        Config $config,
        CodeHelper $codeHelper,
        PathHelper $pathHelper,
        FindAndReplaceHelper $findAndReplaceHelper,
        TypeHelper $typeHelper
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
        $this->typeHelper = $typeHelper;
    }


    /**
     * Generate a new Field based on a property name and Doctrine Type or Archetype field FQN
     *
     * @see MappingHelper::ALL_DBAL_TYPES for the full list of Dbal Types
     *
     * @param string      $fieldFqn
     * @param string      $fieldType
     * @param null|string $phpType
     *
     * @param mixed       $defaultValue
     * @param bool        $isUnique
     *
     * @return string - The Fully Qualified Name of the generated Field Trait
     *
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function generateField(
        string $fieldFqn,
        string $fieldType,
        ?string $phpType = null,
        $defaultValue = null,
        bool $isUnique = false
    ): string {
        $this->validateArguments($fieldFqn, $fieldType, $phpType);
        $this->setupClassProperties($fieldFqn, $fieldType, $phpType, $defaultValue, $isUnique);

        $this->ensurePathExists($this->fieldsPath);
        $this->ensurePathExists($this->fieldsInterfacePath);

        if (true === $this->isArchetype) {
            return $this->createFieldFromArchetype();
        }

        $this->generateInterface();

        return $this->generateTrait();
    }

    protected function createFieldFromArchetype(): string
    {
        $traitPath = $this->getTraitPath();
        $this->assertFileDoesNotExist($traitPath, 'Trait');
        $interfacePath = $this->getInterfacePath();
        $this->assertFileDoesNotExist($interfacePath, 'Interface');
        $copier = new ArchetypeFieldGenerator(
            $this->fileSystem,
            $this->namespaceHelper,
            $this->fieldFqn,
            $traitPath,
            $interfacePath,
            '\\'.$this->fieldType,
            $this->projectRootNamespace
        );

        return $copier->createFromArchetype().self::FIELD_TRAIT_SUFFIX;
    }

    protected function validateArguments(
        string $fieldFqn,
        string $fieldType,
        ?string $phpType
    ): void {
        if (false === strpos($fieldFqn, AbstractGenerator::ENTITY_FIELD_TRAIT_NAMESPACE)) {
            throw new \InvalidArgumentException(
                'Fully qualified name [ '.$fieldFqn.' ]'
                .' does not include [ '.AbstractGenerator::ENTITY_FIELD_TRAIT_NAMESPACE.' ].'."\n"
                .'Please ensure you pass in the full namespace qualified field name'
            );
        }
        if (
            false === \in_array($fieldType, MappingHelper::ALL_DBAL_TYPES, true)
            && false === \in_array($fieldType, self::STANDARD_FIELDS, true)
            && false === $this->traitFqnLooksLikeField($fieldType)
        ) {
            throw new \InvalidArgumentException(
                'fieldType '.$fieldType.' is not a valid field type'
            );
        }
        if ((null !== $phpType)
            && (false === \in_array($phpType, MappingHelper::PHP_TYPES, true))
        ) {
            throw new \InvalidArgumentException(
                'phpType must be either null or one of MappingHelper::PHP_TYPES'
            );
        }
    }

    /**
     * Does the specified trait FQN look like a field trait?
     *
     * @param string $traitFqn
     *
     * @return bool
     * @throws \ReflectionException
     */
    protected function traitFqnLooksLikeField(string $traitFqn): bool
    {
        try {
            $reflection = new \ReflectionClass($traitFqn);
        } catch (\ReflectionException $e) {
            throw new \InvalidArgumentException('invalid traitFqn '.$traitFqn.' does not seem to exist');
        }
        if (true !== $reflection->isTrait()) {
            throw new \InvalidArgumentException('field type is not a trait FQN');
        }
        if ('FieldTrait' !== substr($traitFqn, -\strlen('FieldTrait'))) {
            throw new \InvalidArgumentException('traitFqn does not end in FieldTrait');
        }

        return true;
    }

    /**
     * Defining the properties for the field to be generated
     *
     * @param string      $fieldFqn
     * @param string      $fieldType
     * @param null|string $phpType
     * @param mixed       $defaultValue
     * @param bool        $isUnique
     *
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    protected function setupClassProperties(
        string $fieldFqn,
        string $fieldType,
        ?string $phpType,
        $defaultValue,
        bool $isUnique
    ) {
        $this->fieldType = $fieldType;
        if (true !== \in_array($this->fieldType, MappingHelper::COMMON_TYPES, true)) {
            $this->isArchetype = true;
        }
        $this->phpType      = $phpType ?? $this->getPhpTypeForDbalType();
        $this->defaultValue = $this->typeHelper->normaliseValueToType($defaultValue, $this->phpType);

        if (null !== $this->defaultValue) {
            $defaultValueType = $this->typeHelper->getType($this->defaultValue);
            if ($defaultValueType !== $this->phpType) {
                throw new \InvalidArgumentException(
                    'default value '.$this->defaultValue.' has the type: '.$defaultValueType
                    .' whereas the phpType for this field has been set as '.$this->phpType.', these do not match up'
                );
            }
        }
        $this->isNullable = (null === $defaultValue);
        $this->isUnique   = $isUnique;

        if (substr($fieldFqn, -strlen(self::FIELD_TRAIT_SUFFIX)) === self::FIELD_TRAIT_SUFFIX) {
            $fieldFqn = substr($fieldFqn, 0, -\strlen(self::FIELD_TRAIT_SUFFIX));
        }
        $this->fieldFqn = $fieldFqn;

        list($className, $traitNamespace, $traitSubDirectories) = $this->parseFullyQualifiedName(
            $fieldFqn,
            $this->srcSubFolderName
        );
        list(, $interfaceNamespace, $interfaceSubDirectories) = $this->parseFullyQualifiedName(
            str_replace('Traits', 'Interfaces', $fieldFqn),
            $this->srcSubFolderName
        );

        $this->fieldsPath = $this->codeHelper->resolvePath(
            $this->pathToProjectRoot.'/'.implode('/', $traitSubDirectories)
        );

        $this->fieldsInterfacePath = $this->codeHelper->resolvePath(
            $this->pathToProjectRoot.'/'.implode('/', $interfaceSubDirectories)
        );

        $this->classy             = Inflector::classify($className);
        $this->consty             = strtoupper(Inflector::tableize($className));
        $this->traitNamespace     = $traitNamespace;
        $this->interfaceNamespace = $interfaceNamespace;
    }


    /**
     * @param string $path
     */
    protected function ensurePathExists(string $path): void
    {
        if ($this->fileSystem->exists($path)) {
            return;
        }
        $this->fileSystem->mkdir($path);
    }


    /**
     * @return string
     * @throws DoctrineStaticMetaException
     *
     */
    protected function getPhpTypeForDbalType(): string
    {
        if (true === $this->isArchetype) {
            return '';
        }
        if (!\in_array($this->fieldType, MappingHelper::COMMON_TYPES, true)) {
            throw new DoctrineStaticMetaException(
                'Field type of '.$this->fieldType.' is not one of MappingHelper::COMMON_TYPES'
                ."\n\nYou can only use this fieldType type if you pass in the explicit phpType as well "
                ."\n\nAlternatively, suggest you set the type as string and then edit the generated code as you see fit"
            );
        }

        return MappingHelper::COMMON_TYPES_TO_PHP_TYPES[$this->fieldType];
    }

    protected function getInterfacePath(): string
    {
        return $this->fieldsInterfacePath.'/'.$this->classy.'FieldInterface.php';
    }

    /**
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    protected function generateInterface(): void
    {
        $filePath = $this->getInterfacePath();
        $this->assertFileDoesNotExist($filePath, 'Interface');
        try {
            $this->fileSystem->copy(
                $this->codeHelper->resolvePath(static::FIELD_INTERFACE_TEMPLATE_PATH),
                $filePath
            );
            $this->interfacePostCopy($filePath);
            $this->codeHelper->replaceTypeHintsInFile(
                $filePath,
                $this->phpType,
                $this->fieldType,
                $this->isNullable
            );
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Error in '.__METHOD__.': '.$e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @param string $filePath
     *
     * @throws \RuntimeException
     * @throws DoctrineStaticMetaException
     */
    protected function postCopy(string $filePath): void
    {
        $this->fileCreationTransaction::setPathCreated($filePath);
        $this->findAndReplaceHelper->replaceName(
            $this->classy,
            $filePath,
            static::FIND_ENTITY_FIELD_NAME
        );
        $this->findAndReplaceHelper->findReplace('TEMPLATE_FIELD_NAME', $this->consty, $filePath);
        $this->codeHelper->tidyNamespacesInFile($filePath);
        $this->setGetterToIsForBools($filePath);
    }

    /**
     * @param string $filePath
     *
     * @throws \RuntimeException
     * @throws DoctrineStaticMetaException
     */
    protected function traitPostCopy(string $filePath): void
    {
        $this->findAndReplaceHelper->replaceFieldTraitNamespace($this->traitNamespace, $filePath);
        $this->findAndReplaceHelper->replaceFieldInterfaceNamespace($this->interfaceNamespace, $filePath);
        $this->postCopy($filePath);
    }

    protected function setGetterToIsForBools(string $filePath): void
    {
        if ($this->phpType !== 'bool') {
            return;
        }
        $replaceName = $this->codeHelper->getGetterMethodNameForBoolean($this->classy);
        $findName    = 'get'.$this->classy;
        $this->findAndReplaceHelper->findReplace($findName, $replaceName, $filePath);
    }

    /**
     * @param string $filePath
     *
     * @throws DoctrineStaticMetaException
     */
    protected function interfacePostCopy(string $filePath): void
    {
        $this->findAndReplaceHelper->replaceFieldInterfaceNamespace($this->interfaceNamespace, $filePath);
        $this->replaceDefaultValueInInterface($filePath);
        $this->postCopy($filePath);
    }

    /**
     * @param string $filePath
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function replaceDefaultValueInInterface(string $filePath): void
    {
        $defaultType = $this->typeHelper->getType($this->defaultValue);
        switch (true) {
            case $defaultType === 'null':
                $replace = 'null';
                break;
            case $this->phpType === 'string':
                $replace = "'$this->defaultValue'";
                break;
            case $this->phpType === 'bool':
                $replace = true === $this->defaultValue ? 'true' : 'false';
                break;
            case $this->phpType === 'float':
                $replace = (string)$this->defaultValue;
                if (false === strpos($replace, '.')) {
                    $replace .= '.0';
                }
                break;
            case $this->phpType === 'int':
                $replace = (string)$this->defaultValue;
                break;
            case $this->phpType === 'DateTime':
                if ($this->defaultValue !== MappingHelper::DATETIME_DEFAULT_CURRENT_TIME_STAMP) {
                    throw new \InvalidArgumentException(
                        'Invalid default value '.$this->defaultValue
                        .'We only support current timestamp as the default on DateTime'
                    );
                }
                $replace = "\EdmondsCommerce\DoctrineStaticMeta\MappingHelper::DATETIME_DEFAULT_CURRENT_TIME_STAMP";
                break;
            default:
                throw new \RuntimeException(
                    'failed to calculate replace based on defaultType '.$defaultType
                    .' and phpType '.$this->phpType.' in '.__METHOD__
                );
        }
        $this->findAndReplaceHelper->findReplace("'defaultValue'", $replace, $filePath);
    }

    protected function getTraitPath(): string
    {
        return $this->fieldsPath.'/'.$this->classy.'FieldTrait.php';
    }

    /**
     * @return string
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    protected function generateTrait(): string
    {
        $filePath = $this->getTraitPath();
        $this->assertFileDoesNotExist($filePath, 'Trait');
        try {
            $this->fileSystem->copy(
                $this->codeHelper->resolvePath(static::FIELD_TRAIT_TEMPLATE_PATH),
                $filePath
            );
            $this->fileCreationTransaction::setPathCreated($filePath);
            $this->traitPostCopy($filePath);
            $trait = PhpTrait::fromFile($filePath);
            $trait->setMethod($this->getPropertyMetaMethod());
            $trait->addUseStatement('\\'.MappingHelper::class);
            $trait->addUseStatement('\\'.ClassMetadataBuilder::class);
            $this->codeHelper->generate($trait, $filePath);
            $this->codeHelper->replaceTypeHintsInFile(
                $filePath,
                $this->phpType,
                $this->fieldType,
                $this->isNullable
            );

            return $trait->getQualifiedName();
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Error in '.__METHOD__.': '.$e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @return PhpMethod
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    protected function getPropertyMetaMethod(): PhpMethod
    {
        $name   = UsesPHPMetaDataInterface::METHOD_PREFIX_GET_PROPERTY_DOCTRINE_META.$this->classy;
        $method = PhpMethod::create($name);
        $method->setStatic(true);
        $method->setVisibility('public');
        $method->setParameters(
            [PhpParameter::create('builder')->setType('ClassMetadataBuilder')]
        );
        $mappingHelperMethodName = 'setSimple'.ucfirst(strtolower($this->fieldType)).'Fields';

        $methodBody = "
        MappingHelper::$mappingHelperMethodName(
            [{$this->classy}FieldInterface::PROP_{$this->consty}],
            \$builder,
            {$this->classy}FieldInterface::DEFAULT_{$this->consty}
        );                        
";
        if (\in_array($this->fieldType, MappingHelper::UNIQUEABLE_TYPES, true)) {
            $isUniqueString = $this->isUnique ? 'true' : 'false';
            $methodBody     = "
        MappingHelper::$mappingHelperMethodName(
            [{$this->classy}FieldInterface::PROP_{$this->consty}],
            \$builder,
            {$this->classy}FieldInterface::DEFAULT_{$this->consty},
            $isUniqueString
        );                        
";
        }
        $method->setBody($methodBody);
        $method->setDocblock(
            DocBlock::create()
                    ->appendTag(
                        UnknownTag::create('SuppressWarnings(PHPMD.StaticAccess)')
                    )
        );

        return $method;
    }

    private function assertFileDoesNotExist(string $filePath, string $type): void
    {
        if (file_exists($filePath)) {
            throw new \RuntimeException("Field $type already exists at $filePath");
        }
    }
}
