<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Fields;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\AbstractCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\Pipeline;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ReplaceNameProcess;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\Writer;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use InvalidArgumentException;
use function ts\arrayContains;

abstract class AbstractFieldCreator extends AbstractCreator
{
    public const SUFFIX    = 'overridden';
    public const FIND_NAME = 'TemplateFieldName';
    public const FIND_TYPE = 'string';

    /**
     * @var string
     */
    protected $phpType = MappingHelper::PHP_TYPE_STRING;
    /**
     * @var string
     */
    protected $mappingHelperType = MappingHelper::TYPE_STRING;
    /**
     * @var mixed|null
     */
    protected $defaultValue;
    /**
     * @var string
     */
    protected $baseName;

    /**
     * @var CodeHelper
     */
    protected $codeHelper;
    /**
     * @var bool
     */
    protected $isUnique = false;

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

    /**
     * @param bool $isUnique
     *
     * @return $this
     */
    public function setUnique(bool $isUnique)
    {
        $this->isUnique = $isUnique;

        return $this;
    }

    /**
     * @param mixed $defaultValue
     *
     * @return $this
     */
    public function setDefaultValue($defaultValue)
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

    protected function registerReplaceType(): void
    {
        $process = new ReplaceNameProcess();
        $process->setArgs(self::FIND_TYPE, $this->phpType);
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
