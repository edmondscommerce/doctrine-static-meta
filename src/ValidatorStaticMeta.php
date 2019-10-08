<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ValidatedEntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Exception;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use ts\Reflection\ReflectionMethod;

use function stripos;

/**
 * This class generates and represents the static meta data that is used for validating a specific Entity FQN
 */
class ValidatorStaticMeta
{
    /**
     * @var DoctrineStaticMeta
     */
    private $doctrineStaticMeta;

    public function __construct(DoctrineStaticMeta $doctrineStaticMeta)
    {
        $this->doctrineStaticMeta = $doctrineStaticMeta;
    }

    public function addValidatorMetaData(ClassMetadata $metadata): void
    {
        $methodName = '__no_method__';
        try {
            $staticMethods = $this->doctrineStaticMeta->getStaticMethods();
            //now loop through and call them
            foreach ($staticMethods as $method) {
                $methodName = $method->getName();
                if ($this->methdodNameStartsWithValidatorMetaPrefix($methodName)) {
                    $this->callMetaDataMethodOnEntity($method, $metadata);
                }
            }
        } catch (Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in ' . __METHOD__ . 'for '
                . self::class . "::$methodName\n\n"
                . $e->getMessage()
            );
        }
    }

    private function methdodNameStartsWithValidatorMetaPrefix(string $methodName): bool
    {
        if (
            0 === stripos(
                $methodName,
                ValidatedEntityInterface::METHOD_PREFIX_PROPERTY_VALIDATOR_META
            )
            ||
            0 === stripos(
                $methodName,
                ValidatedEntityInterface::METHOD_PREFIX_ENTITY_VALIDATOR_META
            )
        ) {
            return true;
        }

        return false;
    }

    private function callMetaDataMethodOnEntity(ReflectionMethod $method, ClassMetadata $metadata): void
    {
        $method->setAccessible(true);
        $method->invokeArgs(null, [$metadata]);
    }
}
