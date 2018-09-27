<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\Src\Entity;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ProcessInterface;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\ReflectionHelper;
use EdmondsCommerce\DoctrineStaticMeta\DoctrineStaticMeta;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use ts\Reflection\ReflectionClass;

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

    private $imports = [];

    private $properties = [];

    private $setters = [];

    private $getters = [];

    public function __construct(ReflectionHelper $reflectionHelper)
    {
        $this->reflectionHelper = $reflectionHelper;
    }

    public function setEntityFqn(string $entityFqn)
    {
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
            $this->setSetterBodyFromTrait($trait, $setterName, $property);
            $this->setGetterBodyFromTrait($trait, $property, $getterName);
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
            if (!\in_array($type, MappingHelper::PHP_TYPES, true)) {
                $this->imports["use $type;"] = "use $type;";
                $type                        = (new ReflectionClass($type))->getShortName();
            }
        }

        return [$property, (string)$type];
    }

    private function setProperty(string $property, string $type): void
    {
        $code               = '';
        $code               .= "\n" . '    /**';
        $code               .= "\n" . '     * @var ' . $type;
        $code               .= "\n" . '     */';
        $code               .= "\n" . '    private $' . $property . ';';
        $this->properties[] = $code;
    }

    private function setSetterBodyFromTrait(ReflectionClass $trait, string $setterName, string $property): void
    {
        $methodBody = $this->getMethodBodyFromTrait($setterName, $trait);

        $methodBody      = preg_replace(
            '%{.+}%s',
            "{\n        \$this->$property=\$$property;\n        return \$this;\n    }",
            $methodBody
        );
        $methodBody      = preg_replace('%,.+?bool \$recip.+?=.+?true%s', '', $methodBody);
        $methodBody      = str_replace('private function', 'public function', $methodBody);
        $methodBody      = preg_replace('%\): .+?\{%s', "): self\n    {", $methodBody);
        $this->setters[] = $methodBody;

    }

    private function getMethodBodyFromTrait($methodName, ReflectionClass $trait): string
    {
        $methodBody = $this->reflectionHelper->getMethodBody($methodName, $trait);
        if ('' !== $methodBody) {
            return $methodBody;
        }
        foreach ($trait->getTraits() as $parentTrait) {
            $methodBody = $this->reflectionHelper->getMethodBody($methodName, $parentTrait);
            if ('' !== $methodBody) {
                return $methodBody;
            }
        }
        throw new \RuntimeException(
            'Failed getting method body for method ' . $methodName . ' from trait ' . $trait->getName()
        );
    }

    private function setGetterBodyFromTrait(
        ReflectionClass $trait,
        string $property,
        string $getterName
    ): void {
        $methodBody = $this->getMethodBodyFromTrait($getterName, $trait);
        $methodBody = preg_replace(
            '%\{.+\}%s',
            "{\n        return \$this->$property;\n    }",
            $methodBody
        );

        $this->getters[] = $methodBody;
    }

    private function updateFileContents(File\FindReplace $findReplace)
    {
        $body = '{'
                . implode("\n", $this->properties) .
                "\n\n" .
                implode("\n", $this->getters) .
                "\n" .
                implode("\n", $this->setters) .
                "\n}";

        $findReplace->findReplaceRegex('%{.+?}%s', $body);

        $useStatements = "\n" . implode("\n", $this->imports) . "\n";
        $findReplace->findReplace(
            'use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;',
            $useStatements . "\n"
            . 'use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;'
        );
    }


}