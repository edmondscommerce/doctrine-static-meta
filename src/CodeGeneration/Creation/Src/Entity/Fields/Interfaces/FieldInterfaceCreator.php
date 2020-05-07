<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Fields\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\AbstractCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\FindReplaceProcess;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Fields\AbstractFieldCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Fields\Traits\FieldTraitCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\Writer;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\TypeHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use InvalidArgumentException;
use RuntimeException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FieldInterfaceCreator extends AbstractFieldCreator
{
    public const TEMPLATE_PATH = self::ROOT_TEMPLATE_PATH .
                                 '/src/Entity/Fields/Interfaces/' .
                                 self::FIND_NAME . 'FieldInterface.php';


    public const SUFFIX = FieldTraitCreator::INTERFACE_SUFFIX;
    /**
     * @var TypeHelper
     */
    private TypeHelper $typeHelper;

    public function __construct(
        FileFactory $fileFactory,
        NamespaceHelper $namespaceHelper,
        Writer $fileWriter,
        Config $config,
        FindReplaceFactory $findReplaceFactory,
        CodeHelper $codeHelper,
        TypeHelper $typeHelper
    ) {
        parent::__construct($fileFactory, $namespaceHelper, $fileWriter, $config, $findReplaceFactory, $codeHelper);
        $this->typeHelper = $typeHelper;
    }


    public function setNewObjectFqn(string $newObjectFqn): AbstractCreator
    {
        $this->validateFqnEndsWithSuffix($newObjectFqn);

        return parent::setNewObjectFqn($newObjectFqn);
    }

    private function validateFqnEndsWithSuffix(string $newObjectFqn): void
    {
        if (substr($newObjectFqn, 0 - strlen(self::SUFFIX)) === self::SUFFIX) {
            return;
        }
        throw new InvalidArgumentException('$newObjectFqn must end in ' . self::SUFFIX);
    }


    protected function configurePipeline(): void
    {
        parent::configurePipeline();
        $this->registerReplaceDefaultValue();
        $this->registerReplacePropertyName();
        $this->registerReplaceType();
    }

    private function registerReplaceDefaultValue(): void
    {
        $find    = "'defaultValue'";
        $replace = $this->getReplaceForDefaultValue();
        $process = new FindReplaceProcess($find, $replace);
        $this->pipeline->register($process);
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return string
     */
    private function getReplaceForDefaultValue(): string
    {
        $defaultType = $this->typeHelper->getType($this->defaultValue);
        switch (true) {
            case $defaultType === 'null':
                return 'null';
            case $this->phpType === 'string':
                return "'$this->defaultValue'";
            case $this->phpType === 'bool':
                return (true === $this->defaultValue) ? 'true' : 'false';
                break;
            case $this->phpType === 'float':
                $replace = (string)$this->defaultValue;
                if (false === \ts\stringContains($replace, '.')) {
                    $replace .= '.0';
                }

                return $replace;
            case $this->phpType === 'int':
                return (string)$this->defaultValue;
            case $this->phpType === trim(MappingHelper::PHP_TYPE_DATETIME, '\\'):
                if ($this->defaultValue !== null) {
                    throw new InvalidArgumentException(
                        'Invalid default value ' . $this->defaultValue
                        . 'Currently we only support null as a default for DateTime'
                    );
                }

                return 'null';
            default:
                throw new RuntimeException(
                    'failed to calculate replace based on defaultType ' . $defaultType
                    . ' and phpType ' . $this->phpType . ' in ' . __METHOD__
                );
        }
    }
}
