<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\Src\Entity;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ProcessInterface;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\ReflectionHelper;
use EdmondsCommerce\DoctrineStaticMeta\DoctrineStaticMeta;

class CreateDataTransferObjectBodyProcess implements ProcessInterface
{
    /**
     * @var DoctrineStaticMeta
     */
    private $dsm;
    /**
     * @var ReflectionHelper
     */
    private $reflectionHelper;

    /**
     * @var array
     */
    private $properties = [];

    /**
     * @var array
     */
    private $setters = [];

    /**
     * @var array
     */
    private $getters = [];
    /**
     * @var CodeHelper
     */
    private $codeHelper;
    /**
     * @var string
     */
    private $entityFqn;

    public function __construct(
        ReflectionHelper $reflectionHelper,
        CodeHelper $codeHelper
    ) {
        $this->reflectionHelper = $reflectionHelper;
        $this->codeHelper       = $codeHelper;
    }

    public function setEntityFqn(string $entityFqn)
    {
        $this->entityFqn = $entityFqn;
        if (false === \ts\stringContains($entityFqn, '\\Entities\\')) {
            throw new \RuntimeException(
                'This does not look like an Entity FQN: ' . $entityFqn
            );
        }
        $this->dsm = new DoctrineStaticMeta($entityFqn);

        return $this;
    }

    public function run(File\FindReplace $findReplace): void
    {
        $this->buildArraysOfCode();
        $this->updateFileContents($findReplace);
    }

    private function buildArraysOfCode()
    {
        foreach ($this->dsm->getSetters() as $getterName => $setterName) {
            $trait  = $this->reflectionHelper->getTraitImplementingMethod(
                $this->dsm->getReflectionClass(),
                $setterName
            );
            $setter = $trait->getMethod($setterName);
            list($property, $type) = $this->getPropertyNameAndTypeFromSetter($setter);
            $this->setProperty($property, $type);
            $this->setGetterFromPropertyAndType($getterName, $property, $type);
            $this->setSetterFromPropertyAndType($setterName, $property, $type);
        }
    }

    private function getPropertyNameAndTypeFromSetter(\ReflectionMethod $setter): array
    {
        /**
         * @var \ReflectionParameter $param
         */
        $param    = current($setter->getParameters());
        $property = $param->getName();
        $type     = $param->getType();
        if (null !== $type) {
            $type = $type->getName();
            if (!\in_array($type, ['string', 'bool', 'int', 'float'])) {
                $type = "\\$type";
            }
            if ($param->allowsNull()) {
                $type = "?$type";
            }
        }

        return [$property, (string)$type];
    }

    private function setProperty(string $property, string $type): void
    {
        $defaultValue       = $this->getDefaultValueCodeForProperty($property);
        $code               = '';
        $code               .= "\n" . '    /**';
        $code               .= ('' !== $type) ? "\n" . '     * @var ' . $type : '';
        $code               .= "\n" . '     */';
        $code               .= "\n" . '    private $' . $property . ' = ' . $defaultValue . ';';
        $this->properties[] = $code;
    }

    private function getDefaultValueCodeForProperty(string $property)
    {
        $defaultValueConst = 'DEFAULT_' . $this->codeHelper->consty($property);
        $fullValueString   = $this->entityFqn . '::' . $defaultValueConst;
        if (\defined($fullValueString)) {
            return $this->dsm->getShortName() . '::' . $defaultValueConst;
        }

        return 'null';
    }

    private function setGetterFromPropertyAndType(string $getterName, string $property, string $type)
    {
        $code            = '';
        $code            .= "\n    public function $getterName()" . (('' !== $type) ? ": $type" : '');
        $code            .= "\n    {";
        $code            .= "\n        return \$this->$property;";
        $code            .= "\n    }\n";
        $this->getters[] = $code;

    }

    private function setSetterFromPropertyAndType(string $setterName, string $property, string $type)
    {
        $code            = '';
        $code            .= "\n    public function $setterName($type \$$property): self ";
        $code            .= "\n    {";
        $code            .= "\n        \$this->$property = \$$property;";
        $code            .= "\n        return \$this;";
        $code            .= "\n    }\n";
        $this->setters[] = $code;

    }

    private function updateFileContents(File\FindReplace $findReplace)
    {

        $body = implode("\n", $this->properties) .
                "\n\n" .
                implode("\n", $this->getters) .
                "\n" .
                implode("\n", $this->setters);

        $findReplace->findReplaceRegex('%{(.+)}%s', "{\n\$1\n$body\n}");
    }
}