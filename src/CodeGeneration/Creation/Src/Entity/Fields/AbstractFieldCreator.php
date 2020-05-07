<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Fields;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\AbstractCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\FindReplaceProcess;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\Pipeline;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ReplaceNameProcess;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ReplaceTypeHintsProcess;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\Writer;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use InvalidArgumentException;

use function ts\arrayContains;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class AbstractFieldCreator extends AbstractCreator
{
    public const SUFFIX    = 'overridden';
    public const FIND_NAME = 'TemplateFieldName';
    public const FIND_TYPE = 'string';

    /**
     * @var string
     */
    protected string $phpType = MappingHelper::PHP_TYPE_STRING;
    /**
     * @var string
     */
    protected string $mappingHelperType = MappingHelper::TYPE_STRING;
    /**
     * @var mixed|null
     */
    protected $defaultValue;
    /**
     * @var string
     */
    protected string $baseName;

    /**
     * @var CodeHelper
     */
    protected CodeHelper $codeHelper;
    /**
     * @var bool
     */
    protected bool $isUnique = false;

    /**
     * @var string
     */
    protected string $subNamespace = '';

    public function __construct(
        FileFactory $fileFactory,
        NamespaceHelper $namespaceHelper,
        Writer $fileWriter,
        Config $config,
        FindReplaceFactory $findReplaceFactory,
        CodeHelper $codeHelper
    ) {
        parent::__construct($fileFactory, $namespaceHelper, $fileWriter, $config, $findReplaceFactory);
        $this->codeHelper = $codeHelper;
    }

    public function setNewObjectFqn(string $newObjectFqn): AbstractCreator
    {
        $this->validateCorrectNamespace($newObjectFqn);
        $this->validateFqnEndsWithSuffix($newObjectFqn);
        $this->setSubNamespace($newObjectFqn);

        return parent::setNewObjectFqn($newObjectFqn);
    }

    private function validateCorrectNamespace(string $newObjectFqn): void
    {
        if (1 === preg_match('%Entity\\\Fields\\\(Traits|Interfaces)%', $newObjectFqn)) {
            return;
        }
        throw new InvalidArgumentException(
            'Invalid FQN ' . $newObjectFqn . ', must include Entity\\Fields\\(Traits|Interfaces)\\'
        );
    }

    private function validateFqnEndsWithSuffix(string $newObjectFqn): void
    {
        if (substr($newObjectFqn, 0 - strlen(static::SUFFIX)) === static::SUFFIX) {
            return;
        }
        throw new InvalidArgumentException('$newObjectFqn must end in ' . static::SUFFIX);
    }

    private function setSubNamespace(string $newObjectFqn): void
    {
        $split    = preg_split('%(Traits|Interfaces)%', $newObjectFqn);
        $exploded = explode('\\', $split[1]);
        array_pop($exploded);
        $filtered = array_filter($exploded);
        if ([] === $filtered) {
            return;
        }
        $subNamespace       = implode('\\', $filtered);
        $this->subNamespace = $subNamespace;
    }

    /**
     * @param bool $isUnique
     *
     * @return $this
     */
    public function setUnique(bool $isUnique): self
    {
        $this->isUnique = $isUnique;

        return $this;
    }

    /**
     * @param mixed $defaultValue
     *
     * @return $this
     */
    public function setDefaultValue($defaultValue): self
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    /**
     * @param string $mappingHelperCommonType
     *
     * @return $this
     */
    public function setMappingHelperCommonType(string $mappingHelperCommonType): self
    {
        $this->validateType($mappingHelperCommonType);
        $this->mappingHelperType = $mappingHelperCommonType;
        $this->phpType           = MappingHelper::COMMON_TYPES_TO_PHP_TYPES[$mappingHelperCommonType];

        return $this;
    }

    protected function validateType(string $mappingHelperCommonType): void
    {
        if (arrayContains($mappingHelperCommonType, MappingHelper::COMMON_TYPES)) {
            return;
        }
        throw new InvalidArgumentException(
            'Invalid type ' . $mappingHelperCommonType . ', must be one of MappingHelper::COMMON_TYPES'
        );
    }

    protected function configurePipeline(): void
    {
        $this->baseName = $this->namespaceHelper->basename($this->newObjectFqn);
        $this->pipeline = new Pipeline($this->findReplaceFactory);
        $this->registerReplaceAccessorForBoolType();
        $this->registerReplaceProjectRootNamespace();
        $this->registerDeeplyNestedNamespaceProcess();
    }

    protected function registerReplaceAccessorForBoolType(): void
    {
        if (MappingHelper::TYPE_BOOLEAN !== $this->mappingHelperType) {
            return;
        }
        $process       = new ReplaceNameProcess();
        $propertyName  = str_replace(static::SUFFIX, '', $this->baseName);
        $replaceMethod = $this->codeHelper->getGetterMethodNameForBoolean($propertyName);
        $process->setArgs('get' . static::FIND_NAME, $replaceMethod);
        $this->pipeline->register($process);
    }

    protected function registerDeeplyNestedNamespaceProcess(): void
    {
        if ('' === $this->subNamespace) {
            return;
        }
        $find    = 'Entity\\Fields\\Traits';
        $replace = $find . '\\' . $this->subNamespace;
        $process = new FindReplaceProcess($find, $replace);
        $this->pipeline->register($process);

        $find    = 'Entity\\Fields\\Interfaces';
        $replace = $find . '\\' . $this->subNamespace;
        $process = new FindReplaceProcess($find, $replace);
        $this->pipeline->register($process);
    }

    protected function registerReplaceType(): void
    {
        $process = new ReplaceTypeHintsProcess(
            $this->codeHelper,
            $this->phpType,
            $this->mappingHelperType,
            $this->defaultValue
        );
        $this->pipeline->register($process);
    }

    protected function registerReplacePropertyName(): void
    {
        $find         = str_replace(static::SUFFIX, '', self::FIND_NAME);
        $propertyName = str_replace(static::SUFFIX, '', $this->baseName);
        $replaceName  = new ReplaceNameProcess();
        $replaceName->setArgs($find, $propertyName);
        $this->pipeline->register($replaceName);
    }
}
