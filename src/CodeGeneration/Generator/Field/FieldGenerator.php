<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FileCreationTransaction;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FindAndReplaceHelper;
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
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
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
    /**
     * @var string
     */
    protected $className;


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
     * @throws \ReflectionException
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

    protected function getTraitPath(): string
    {
        return $this->fieldsPath.'/'.$this->codeHelper->classy($this->className).'FieldTrait.php';
    }


    protected function getInterfacePath(): string
    {
        return $this->fieldsInterfacePath.'/'.$this->codeHelper->classy($this->className).'FieldInterface.php';
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

    /**
     * @return string
     * @throws \ReflectionException
     */
    protected function createFieldFromArchetype(): string
    {
        $copier = new ArchetypeFieldGenerator(
            $this->fileSystem,
            $this->namespaceHelper,
            $this->codeHelper,
            $this->findAndReplaceHelper
        );

        return $copier->createFromArchetype(
            $this->fieldFqn,
            $this->getTraitPath(),
            $this->getInterfacePath(),
            '\\'.$this->fieldType,
            $this->projectRootNamespace
        ).self::FIELD_TRAIT_SUFFIX;
    }

    protected function validateArguments(
        string $fieldFqn,
        string $fieldType,
        ?string $phpType
    ): void {
        //Check for a correct looking field FQN
        if (false === \strpos($fieldFqn, AbstractGenerator::ENTITY_FIELD_TRAIT_NAMESPACE)) {
            throw new \InvalidArgumentException(
                'Fully qualified name [ '.$fieldFqn.' ]'
                .' does not include [ '.AbstractGenerator::ENTITY_FIELD_TRAIT_NAMESPACE.' ].'."\n"
                .'Please ensure you pass in the full namespace qualified field name'
            );
        }
        //Check that the field type is either a Dbal Type or a Field Archetype FQN
        if (false === \in_array($fieldType, MappingHelper::ALL_DBAL_TYPES, true)
            && false === \in_array($fieldType, self::STANDARD_FIELDS, true)
            && false === $this->traitFqnLooksLikeField($fieldType)
        ) {
            throw new \InvalidArgumentException(
                'fieldType '.$fieldType.' is not a valid field type'
            );
        }
        //Check the phpType is valid
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
            throw new \InvalidArgumentException(
                'invalid traitFqn '.$traitFqn.' does not seem to exist',
                $e->getCode(),
                $e
            );
        }
        if (true !== $reflection->isTrait()) {
            throw new \InvalidArgumentException('field type is not a trait FQN');
        }
        if ('FieldTrait' !== \substr($traitFqn, -\strlen('FieldTrait'))) {
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
    ): void {
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

        if (\substr($fieldFqn, -\strlen(self::FIELD_TRAIT_SUFFIX)) === self::FIELD_TRAIT_SUFFIX) {
            $fieldFqn = \substr($fieldFqn, 0, -\strlen(self::FIELD_TRAIT_SUFFIX));
        }
        $this->fieldFqn = $fieldFqn;

        list($className, $traitNamespace, $traitSubDirectories) = $this->parseFullyQualifiedName(
            $fieldFqn,
            $this->srcSubFolderName
        );
        $this->className = $className;
        list(, $interfaceNamespace, $interfaceSubDirectories) = $this->parseFullyQualifiedName(
            \str_replace('Traits', 'Interfaces', $fieldFqn),
            $this->srcSubFolderName
        );

        $this->fieldsPath = $this->pathHelper->resolvePath(
            $this->pathToProjectRoot.'/'.\implode('/', $traitSubDirectories)
        );

        $this->fieldsInterfacePath = $this->pathHelper->resolvePath(
            $this->pathToProjectRoot.'/'.\implode('/', $interfaceSubDirectories)
        );

        $this->traitNamespace     = $traitNamespace;
        $this->interfaceNamespace = $interfaceNamespace;
    }

    private function assertFileDoesNotExist(string $filePath, string $type): void
    {
        if (file_exists($filePath)) {
            throw new \RuntimeException("Field $type already exists at $filePath");
        }
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
}
