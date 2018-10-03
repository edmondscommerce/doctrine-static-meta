<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Modification;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Factory;
use Nette\PhpGenerator\Helpers;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Parameter;
use Nette\PhpGenerator\PhpLiteral;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\Property;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflection\ReflectionMethod;
use Roave\BetterReflection\Reflection\ReflectionParameter;
use Roave\BetterReflection\Reflection\ReflectionProperty;
use Roave\BetterReflection\Reflection\ReflectionType;

/**
 * This is almost a clone of the Nette Factory, but it uses BetterReflection and has been refactored a bit
 *
 * @see Factory
 */
class CodeGenClassTypeFactory
{
    /**
     * @var NamespaceHelper
     */
    private $namespaceHelper;

    public function __construct(NamespaceHelper $namespaceHelper)
    {
        $this->namespaceHelper = $namespaceHelper;
    }

    public function createFromPath(string $path, string $namespaceRoot): ClassType
    {
        return $this->createFromFqn($this->namespaceHelper->getFqnFromPath($path, $namespaceRoot));
    }

    public function createFromFqn(string $fqn): ClassType
    {
        $reflection = ReflectionClass::createFromName($fqn);

        return $this->createFromBetterReflection($reflection);
    }

    public function createFromBetterReflection(ReflectionClass $reflectionClass): ClassType
    {
        $class = $reflectionClass->isAnonymous()
            ? new ClassType
            : new ClassType($reflectionClass->getShortName(), new PhpNamespace($reflectionClass->getNamespaceName()));
        $class->setType($this->getType($reflectionClass));
        $class->setFinal($reflectionClass->isFinal() && $class->getType() === $class::TYPE_CLASS);
        $class->setAbstract($reflectionClass->isAbstract() && $class->getType() === $class::TYPE_CLASS);

        $interfaceNames = $reflectionClass->getInterfaceNames();
        foreach ($interfaceNames as $interfaceName) {
            $interfaceNames = array_filter($interfaceNames,
                function ($item) use ($interfaceName) {
                    return !is_subclass_of($interfaceName, $item);
                });
        }
        $class->setImplements($interfaceNames);

        $class->setComment(Helpers::unformatDocComment($reflectionClass->getDocComment()));
        if ($reflectionClass->getParentClass()) {
            $class->setExtends($reflectionClass->getParentClass()->getName());
            $class->setImplements(array_diff($class->getImplements(),
                                             $reflectionClass->getParentClass()->getInterfaceNames()));
        }
        $props = $methods = [];
        foreach ($reflectionClass->getProperties() as $prop) {
            if ($prop->isDefault() && $prop->getDeclaringClass()->getName() === $reflectionClass->getName()) {
                $props[] = $this->fromPropertyReflection($prop);
            }
        }
        $class->setProperties($props);
        foreach ($reflectionClass->getMethods() as $method) {
            if ($method->getDeclaringClass()->getName() === $reflectionClass->getName()) {
                $methods[] = $this->fromMethodReflection($method);
            }
        }
        $class->setMethods($methods);
        $class->setConstants($reflectionClass->getConstants());

        return $class;
    }

    private function getType(ReflectionClass $reflectionClass): string
    {
        if ($reflectionClass->isInterface()) {
            return ClassType::TYPE_INTERFACE;
        }
        if ($reflectionClass->isTrait()) {
            return ClassType::TYPE_TRAIT;
        }

        return ClassType::TYPE_CLASS;
    }

    private function fromPropertyReflection(ReflectionProperty $reflectionProperty): Property
    {
        $prop = new Property($reflectionProperty->getName());
        $prop->setValue($reflectionProperty->getDeclaringClass()->getDefaultProperties()[$prop->getName()] ?? null);
        $prop->setStatic($reflectionProperty->isStatic());
        $prop->setVisibility($this->getVisibility($reflectionProperty));
        $prop->setComment(Helpers::unformatDocComment($reflectionProperty->getDocComment()));

        return $prop;
    }

    private function getVisibility(object $reflection): string
    {
        if ($reflection->isPrivate()) {
            return ClassType::VISIBILITY_PRIVATE;
        }
        if ($reflection->isProtected()) {
            return ClassType::VISIBILITY_PROTECTED;
        }

        return ClassType::VISIBILITY_PUBLIC;
    }

    private function fromMethodReflection(ReflectionMethod $reflectionMethod): Method
    {
        $method = new Method($reflectionMethod->getName());
        $method->setParameters(array_map([$this, 'fromParameterReflection'], $reflectionMethod->getParameters()));
        $method->setStatic($reflectionMethod->isStatic());
        $isInterface = $reflectionMethod->getDeclaringClass()->isInterface();
        $method->setVisibility($this->getVisibility($reflectionMethod));
        $method->setFinal($reflectionMethod->isFinal());
        $method->setAbstract($reflectionMethod->isAbstract() && !$isInterface);
        $method->setBody($reflectionMethod->isAbstract() ? null : '');
        $method->setReturnReference($reflectionMethod->returnsReference());
        $method->setVariadic($reflectionMethod->isVariadic());
        $method->setComment(Helpers::unformatDocComment($reflectionMethod->getDocComment()));
        if ($reflectionMethod->hasReturnType()) {
            $returnType = $reflectionMethod->getReturnType();
            if ($returnType instanceof ReflectionType) {
                $method->setReturnType($returnType->__toString());
                $method->setReturnNullable($returnType->allowsNull());
            }
        }

        return $method;
    }

    private function fromParameterReflection(ReflectionParameter $reflectionParameter): Parameter
    {
        $param = new Parameter($reflectionParameter->getName());
        $param->setReference($reflectionParameter->isPassedByReference());
        if ($reflectionParameter->hasType()) {
            $type = $reflectionParameter->getType();
            if ($type instanceof ReflectionType) {
                $param->setTypeHint($type->__toString());
                $param->setNullable($type->allowsNull());
            }
        }
        if ($reflectionParameter->isDefaultValueAvailable()) {
            $param->setDefaultValue($reflectionParameter->isDefaultValueConstant()
                                        ? new PhpLiteral($reflectionParameter->getDefaultValueConstantName())
                                        : $reflectionParameter->getDefaultValue());
            $param->setNullable($param->isNullable() && $param->getDefaultValue() !== null);
        }

        return $param;
    }
}