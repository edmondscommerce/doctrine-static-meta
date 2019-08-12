<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FileCreationTransaction;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FindAndReplaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PathHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\ReflectionHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\TypeHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Boolean\DefaultsDisabledFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Boolean\DefaultsEnabledFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Boolean\DefaultsNullFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\DateTime\DateTimeSettableNoDefaultFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\DateTime\DateTimeSettableOnceFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\BusinessIdentifierCodeFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\CountryCodeFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\EmailAddressFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\EnumFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\IpAddressFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\IsbnFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\LocaleIdentifierFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\NullableStringFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\SettableUuidFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\ShortIndexedRequiredStringFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\UnicodeLanguageIdentifierFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\UniqueStringFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\UrlFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\TimeStamp\CreationTimestampFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use InvalidArgumentException;
use ReflectionException;
use RuntimeException;
use Symfony\Component\Filesystem\Filesystem;
use ts\Reflection\ReflectionClass;
use function implode;
use function in_array;
use function str_replace;
use function strlen;
use function strtolower;
use function substr;

/**
 * Class FieldGenerator
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FieldGenerator extends AbstractGenerator
{
    public const FIELD_FQN_KEY           = 'fieldFqn';
    public const FIELD_TYPE_KEY          = 'fieldType';
    public const FIELD_PHP_TYPE_KEY      = 'fieldPhpType';
    public const FIELD_DEFAULT_VAULE_KEY = 'fieldDefaultValue';
    public const FIELD_IS_UNIQUE_KEY     = 'fieldIsUnique';

    public const FIELD_TRAIT_SUFFIX = 'FieldTrait';
    public const STANDARD_FIELDS    = [
        BusinessIdentifierCodeFieldTrait::class,
        CountryCodeFieldTrait::class,
        CreationTimestampFieldTrait::class,
        DateTimeSettableNoDefaultFieldTrait::class,
        DateTimeSettableOnceFieldTrait::class,
        DefaultsDisabledFieldTrait::class,
        DefaultsEnabledFieldTrait::class,
        DefaultsNullFieldTrait::class,
        EmailAddressFieldTrait::class,
        EnumFieldTrait::class,
        IpAddressFieldTrait::class,
        IsbnFieldTrait::class,
        LocaleIdentifierFieldTrait::class,
        NullableStringFieldTrait::class,
        SettableUuidFieldTrait::class,
        ShortIndexedRequiredStringFieldTrait::class,
        UnicodeLanguageIdentifierFieldTrait::class,
        UniqueStringFieldTrait::class,
        UrlFieldTrait::class,
    ];
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
    /**
     * @var string
     */
    protected $className;
    /**
     * @var ReflectionHelper
     */
    private $reflectionHelper;


    public function __construct(
        Filesystem $filesystem,
        FileCreationTransaction $fileCreationTransaction,
        NamespaceHelper $namespaceHelper,
        Config $config,
        CodeHelper $codeHelper,
        PathHelper $pathHelper,
        FindAndReplaceHelper $findAndReplaceHelper,
        TypeHelper $typeHelper,
        ReflectionHelper $reflectionHelper
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
        $this->typeHelper       = $typeHelper;
        $this->reflectionHelper = $reflectionHelper;
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
     * @throws ReflectionException
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

        $this->pathHelper->ensurePathExists($this->fieldsPath);
        $this->pathHelper->ensurePathExists($this->fieldsInterfacePath);

        $this->assertFileDoesNotExist($this->getTraitPath(), 'Trait');
        $this->assertFileDoesNotExist($this->getInterfacePath(), 'Interface');

        if (true === $this->isArchetype) {
            return $this->createFieldFromArchetype();
        }

        return $this->createDbalField();
    }

    protected function validateArguments(
        string $fieldFqn,
        string $fieldType,
        ?string $phpType
    ): void {
        //Check for a correct looking field FQN
        if (false === \ts\stringContains($fieldFqn, AbstractGenerator::ENTITY_FIELD_TRAIT_NAMESPACE)) {
            throw new InvalidArgumentException(
                'Fully qualified name [ ' . $fieldFqn . ' ]'
                . ' does not include [ ' . AbstractGenerator::ENTITY_FIELD_TRAIT_NAMESPACE . ' ].' . "\n"
                . 'Please ensure you pass in the full namespace qualified field name'
            );
        }
        $fieldShortName = $this->namespaceHelper->getClassShortName($fieldFqn);
        if (preg_match('%^(get|set|is|has)%i', $fieldShortName, $matches)) {
            throw new InvalidArgumentException(
                'Your field short name ' . $fieldShortName
                . ' begins with the forbidden string "' . $matches[1] .
                '", please do not use accessor prefixes in your field name'
            );
        }
        //Check that the field type is either a Dbal Type or a Field Archetype FQN
        if (false === in_array($fieldType, self::STANDARD_FIELDS, true)
            && false === in_array(strtolower($fieldType), MappingHelper::ALL_DBAL_TYPES, true)
            && false === $this->traitFqnLooksLikeField($fieldType)
        ) {
            throw new InvalidArgumentException(
                'fieldType ' . $fieldType . ' is not a valid field type'
            );
        }
        //Check the phpType is valid
        if ((null !== $phpType)
            && (false === in_array($phpType, MappingHelper::PHP_TYPES, true))
        ) {
            throw new InvalidArgumentException(
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
     */
    protected function traitFqnLooksLikeField(string $traitFqn): bool
    {
        try {
            $reflection = new ReflectionClass($traitFqn);
        } catch (ReflectionException $e) {
            throw new InvalidArgumentException(
                'invalid traitFqn ' . $traitFqn . ' does not seem to exist',
                $e->getCode(),
                $e
            );
        }
        if (true !== $reflection->isTrait()) {
            throw new InvalidArgumentException('field type is not a trait FQN');
        }
        if ('FieldTrait' !== substr($traitFqn, -strlen('FieldTrait'))) {
            throw new InvalidArgumentException('traitFqn does not end in FieldTrait');
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
    ): void {
        $this->isArchetype = false;
        $this->fieldType   = strtolower($fieldType);
        if (true !== in_array($this->fieldType, MappingHelper::COMMON_TYPES, true)) {
            $this->isArchetype = true;
            $this->fieldType   = $fieldType;
        }
        $this->phpType      = $phpType ?? $this->getPhpTypeForDbalType();
        $this->defaultValue = $this->typeHelper->normaliseValueToType($defaultValue, $this->phpType);

        if (null !== $this->defaultValue) {
            $defaultValueType = $this->typeHelper->getType($this->defaultValue);
            if ($defaultValueType !== $this->phpType) {
                throw new InvalidArgumentException(
                    'default value ' .
                    $this->defaultValue .
                    ' has the type: ' .
                    $defaultValueType
                    .
                    ' whereas the phpType for this field has been set as ' .
                    $this->phpType .
                    ', these do not match up'
                );
            }
        }
        $this->isNullable = (null === $defaultValue);
        $this->isUnique   = $isUnique;

        if (substr($fieldFqn, -strlen(self::FIELD_TRAIT_SUFFIX)) === self::FIELD_TRAIT_SUFFIX) {
            $fieldFqn = substr($fieldFqn, 0, -strlen(self::FIELD_TRAIT_SUFFIX));
        }
        $this->fieldFqn = $fieldFqn;

        list($className, $traitNamespace, $traitSubDirectories) = $this->parseFullyQualifiedName(
            $this->fieldFqn,
            $this->srcSubFolderName
        );
        $this->className = $className;
        list(, $interfaceNamespace, $interfaceSubDirectories) = $this->parseFullyQualifiedName(
            str_replace('Traits', 'Interfaces', $this->fieldFqn),
            $this->srcSubFolderName
        );

        $this->fieldsPath = $this->pathHelper->resolvePath(
            $this->pathToProjectRoot . '/' . implode('/', $traitSubDirectories)
        );

        $this->fieldsInterfacePath = $this->pathHelper->resolvePath(
            $this->pathToProjectRoot . '/' . implode('/', $interfaceSubDirectories)
        );

        $this->traitNamespace     = $traitNamespace;
        $this->interfaceNamespace = $interfaceNamespace;
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
        if (!in_array($this->fieldType, MappingHelper::COMMON_TYPES, true)) {
            throw new DoctrineStaticMetaException(
                'Field type of ' .
                $this->fieldType .
                ' is not one of MappingHelper::COMMON_TYPES'
                .
                "\n\nYou can only use this fieldType type if you pass in the explicit phpType as well "
                .
                "\n\nAlternatively, suggest you set the type as string and then edit the generated code as you see fit"
            );
        }

        return MappingHelper::COMMON_TYPES_TO_PHP_TYPES[$this->fieldType];
    }

    protected function getTraitPath(): string
    {
        return $this->fieldsPath . '/' . $this->codeHelper->classy($this->className) . 'FieldTrait.php';
    }

    protected function getInterfacePath(): string
    {
        return $this->fieldsInterfacePath . '/' . $this->codeHelper->classy($this->className) . 'FieldInterface.php';
    }

    /**
     * @return string
     * @throws ReflectionException
     */
    protected function createFieldFromArchetype(): string
    {
        $copier = new ArchetypeFieldGenerator(
            $this->fileSystem,
            $this->namespaceHelper,
            $this->codeHelper,
            $this->findAndReplaceHelper,
            $this->reflectionHelper
        );

        return $copier->createFromArchetype(
            $this->fieldFqn,
            $this->getTraitPath(),
            $this->getInterfacePath(),
            '\\' . $this->fieldType,
            $this->projectRootNamespace
        ) . self::FIELD_TRAIT_SUFFIX;
    }

    /**
     * @return string
     * @throws DoctrineStaticMetaException
     */
    protected function createDbalField(): string
    {
        $creator = new DbalFieldGenerator(
            $this->fileSystem,
            $this->codeHelper,
            $this->fileCreationTransaction,
            $this->findAndReplaceHelper,
            $this->typeHelper,
            $this->pathHelper
        );

        return $creator->create(
            $this->className,
            $this->getTraitPath(),
            $this->getInterfacePath(),
            $this->fieldType,
            $this->defaultValue,
            $this->isUnique,
            $this->phpType,
            $this->traitNamespace,
            $this->interfaceNamespace
        );
    }

    private function assertFileDoesNotExist(string $filePath, string $type): void
    {
        if (file_exists($filePath)) {
            throw new RuntimeException("Field $type already exists at $filePath");
        }
    }
}
