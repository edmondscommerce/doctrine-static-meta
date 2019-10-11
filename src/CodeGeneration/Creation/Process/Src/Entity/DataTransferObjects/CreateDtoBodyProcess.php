<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\Src\Entity\DataTransferObjects;

use Doctrine\Common\Collections\Collection;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ProcessInterface;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\ReflectionHelper;
use EdmondsCommerce\DoctrineStaticMeta\DoctrineStaticMeta;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\PrimaryKey\IdFieldInterface;
use ReflectionParameter;
use RuntimeException;
use ts\Reflection\ReflectionMethod;

use function defined;
use function in_array;

class CreateDtoBodyProcess implements ProcessInterface
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
    /**
     * @var NamespaceHelper
     */
    private $namespaceHelper;

    public function __construct(
        ReflectionHelper $reflectionHelper,
        CodeHelper $codeHelper,
        NamespaceHelper $namespaceHelper
    ) {
        $this->reflectionHelper = $reflectionHelper;
        $this->codeHelper       = $codeHelper;
        $this->namespaceHelper  = $namespaceHelper;
    }

    public function setEntityFqn(string $entityFqn)
    {
        $this->entityFqn = $entityFqn;
        if (false === \ts\stringContains($entityFqn, '\\Entities\\')) {
            throw new RuntimeException(
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

    private function buildArraysOfCode(): void
    {
        foreach ($this->dsm->getSetters() as $getterName => $setterName) {
            if ('getId' === $getterName) {
                continue;
            }
            $reflectionClassWithMethod = $this->reflectionHelper->getTraitImplementingMethod(
                $this->dsm->getReflectionClass(),
                $setterName
            );
            if (null === $reflectionClassWithMethod) {
                $reflectionClassWithMethod = $this->dsm->getReflectionClass();
            }
            $setter   = $reflectionClassWithMethod->getMethod($setterName);
            $property = $this->dsm->getPropertyNameFromSetterName($setterName);
            $type     = $this->getPropertyTypeFromSetter($setter);
            $this->setProperty($property, $type);
            $this->setGetterFromPropertyAndType($getterName, $property, $type);
            $this->setSetterFromPropertyAndType($setterName, $property, $type);
            $this->addIssetMethodsForProperty($property, $type);
        }
    }

    private function getPropertyTypeFromSetter(ReflectionMethod $setter): string
    {
        /**
         * @var ReflectionParameter $param
         */
        $param = current($setter->getParameters());
        $type  = $param->getType();
        if (null !== $type) {
            $type = $type->getName();
            if (!in_array($type, ['string', 'bool', 'int', 'float', 'object', 'array'], true)) {
                $type = "\\$type";
            }
            if ($param->allowsNull()) {
                $type = "?$type";
            }
        }

        return (string)$type;
    }

    private function setProperty(string $property, string $type): void
    {
        $defaultValue = $this->getDefaultValueCodeForProperty($property);
        $type         = $this->getPropertyVarType($type);
        $type         = $this->makeIdTypesNullable($property, $type);

        $code = '';
        $code .= "\n" . '    /**';
        $code .= ('' !== $type) ? "\n" . '     * @var ' . $type : '';
        $code .= "\n" . '     */';
        $code .= "\n" . '    private $' . $property . ' = ' . $defaultValue . ';';

        $this->properties[] = $code;
    }

    private function getDefaultValueCodeForProperty(
        string $property
    ): string {
        $defaultValueConst = 'DEFAULT_' . $this->codeHelper->consty($property);
        $fullValueString   = $this->entityFqn . '::' . $defaultValueConst;
        if (defined($fullValueString)) {
            return $this->dsm->getShortName() . '::' . $defaultValueConst;
        }

        return 'null';
    }

    private function getPropertyVarType(string $type): string
    {
        if (false === \ts\stringContains($type, '\\Entity\\Interfaces\\')) {
            return $type;
        }
        $types = [];
        if (0 === strpos($type, '?')) {
            $types[] = 'null';
            $type    = substr($type, 1);
        }
        $types[] = $type;
        $types[] = $this->namespaceHelper->getEntityDtoFqnFromEntityFqn(
            $this->namespaceHelper->getEntityFqnFromEntityInterfaceFqn($type)
        );

        return implode('|', $types);
    }

    private function makeIdTypesNullable(string $property, string $type): string
    {
        if (IdFieldInterface::PROP_ID === $property && 0 !== strpos($type, '?')) {
            $type = "?$type";
        }

        return $type;
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @param string $getterName
     * @param string $property
     * @param string $type
     */
    private function setGetterFromPropertyAndType(
        string $getterName,
        string $property,
        string $type
    ): void {
        $type            = $this->makeIdTypesNullable($property, $type);
        $code            = '';
        $code            .= "\n    public function $getterName()" . (('' !== $type) ? ": $type" : '');
        $code            .= "\n    {";
        $code            .= $this->getGetterBody($property, $type);
        $code            .= "\n    }\n";
        $this->getters[] = $code;
    }

    private function getGetterBody(
        string $property,
        string $type
    ): string {
        if ('\\' . Collection::class === $type) {
            return "\n        return \$this->$property ?? \$this->$property = new ArrayCollection();";
        }
        if (\ts\stringContains($type, '\\Entity\\Interfaces\\')) {
            $getterCode = '';
            $getterCode .= "\n        if(null === \$this->$property){";
            $getterCode .= "\n            return \$this->$property;";
            $getterCode .= "\n        }";
            if (0 === strpos($type, '?')) {
                $type = substr($type, 1);
            }
            $getterCode .= "\n        if(\$this->$property instanceof $type){";
            $getterCode .= "\n            return \$this->$property;";
            $getterCode .= "\n        }";
            $getterCode .= "\n        throw new \RuntimeException(";
            $getterCode .= "\n            '\$this->$property is not an Entity, but is '. \get_class(\$this->$property)";
            $getterCode .= "\n        );";

            return $getterCode;
        }

        return "\n        return \$this->$property;";
    }

    private function setSetterFromPropertyAndType(
        string $setterName,
        string $property,
        string $type
    ): void {
        $code            = '';
        $code            .= "\n    public function $setterName($type \$$property): self ";
        $code            .= "\n    {";
        $code            .= "\n        \$this->$property = \$$property;";
        $code            .= "\n        return \$this;";
        $code            .= "\n    }\n";
        $this->setters[] = $code;
        if (\ts\stringContains($type, '\\Entity\\Interfaces\\')) {
            $this->setDtoGetterAndSetterForEntityProperty($setterName, $property, $type);
        }
    }

    private function setDtoGetterAndSetterForEntityProperty(
        string $setterName,
        string $property,
        string $entityInterfaceFqn
    ): void {
        $dtoFqn          = $this->namespaceHelper->getEntityDtoFqnFromEntityFqn(
            $this->namespaceHelper->getEntityFqnFromEntityInterfaceFqn($entityInterfaceFqn)
        );
        $setterCode      = '';
        $setterCode      .= "\n    public function ${setterName}Dto($dtoFqn \$$property): self ";
        $setterCode      .= "\n    {";
        $setterCode      .= "\n        \$this->$property = \$$property;";
        $setterCode      .= "\n        return \$this;";
        $setterCode      .= "\n    }\n";
        $this->setters[] = $setterCode;

        $getterName = 'get' . substr($setterName, 3);
        $getterCode = '';
        $getterCode .= "\n    public function $getterName" . "Dto(): $dtoFqn";
        $getterCode .= "\n    {";
        $getterCode .= "\n        if(null === \$this->$property){";
        $getterCode .= "\n            return \$this->$property;";
        $getterCode .= "\n        }";
        if (0 === strpos($dtoFqn, '?')) {
            $dtoFqn = substr($dtoFqn, 1);
        }
        $getterCode      .= "\n        if(\$this->$property instanceof $dtoFqn){";
        $getterCode      .= "\n            return \$this->$property;";
        $getterCode      .= "\n        }";
        $getterCode      .= "\n        throw new \RuntimeException(";
        $getterCode      .= "\n            '\$this->$property is not a DTO, but is '. \get_class(\$this->$property)";
        $getterCode      .= "\n        );";
        $getterCode      .= "\n    }\n";
        $this->getters[] = $getterCode;
    }

    private function addIssetMethodsForProperty(string $property, string $type): void
    {
        if (false === \ts\stringContains($type, '\\Entity\\Interfaces\\')) {
            return;
        }
        $methodName      = 'isset' . ucfirst($property) . 'AsDto';
        $getterCodeDto   = '';
        $getterCodeDto   .= "\n    public function $methodName(): bool";
        $getterCodeDto   .= "\n    {";
        $getterCodeDto   .= "\n        return \$this->$property instanceof DataTransferObjectInterface;";
        $getterCodeDto   .= "\n    }\n";
        $this->getters[] = $getterCodeDto;

        $methodName       = 'isset' . ucfirst($property) . 'AsEntity';
        $getterCodeEntity = '';
        $getterCodeEntity .= "\n    public function $methodName(): bool";
        $getterCodeEntity .= "\n    {";
        $getterCodeEntity .= "\n        return \$this->$property instanceof EntityInterface;";
        $getterCodeEntity .= "\n    }\n";
        $this->getters[]  = $getterCodeEntity;
    }

    private function updateFileContents(
        File\FindReplace $findReplace
    ): void {
        sort($this->properties, SORT_STRING);
        sort($this->getters, SORT_STRING);
        sort($this->setters, SORT_STRING);

        $body = implode("\n", $this->properties) .
                "\n\n" .
                implode("\n", $this->getters) .
                "\n" .
                implode("\n", $this->setters);

        $findReplace->findReplaceRegex('%{(.+)}%s', "{\n\$1\n$body\n}");
    }
}
