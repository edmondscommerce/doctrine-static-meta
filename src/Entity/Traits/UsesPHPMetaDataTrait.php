<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata as DoctrineClassMetaData;
use EdmondsCommerce\DoctrineStaticMeta\DoctrineStaticMeta;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;

trait UsesPHPMetaDataTrait
{

    /**
     * @var DoctrineStaticMeta
     */
    private static $doctrineStaticMeta;

    /**
     * Loads the class and property meta data in the class
     *
     * This is the method called by Doctrine to load the meta data
     *
     * @param DoctrineClassMetaData $metaData
     *
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function loadMetadata(DoctrineClassMetaData $metaData): void
    {
        try {
            self::getDoctrineStaticMeta()->setMetaData($metaData)->buildMetaData();
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in ' . __METHOD__ . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @param DoctrineClassMetaData|null $metaData
     *
     * @return DoctrineStaticMeta
     * @throws \ReflectionException
     */
    public static function getDoctrineStaticMeta(): DoctrineStaticMeta
    {
        if (null === self::$doctrineStaticMeta) {
            self::$doctrineStaticMeta = new DoctrineStaticMeta(self::class);
        }

        return self::$doctrineStaticMeta;
    }

    /**
     * In the class itself, we need to specify the repository class name
     *
     * @param ClassMetadataBuilder $builder
     *
     * @return mixed
     */
    abstract protected static function setCustomRepositoryClass(ClassMetadataBuilder $builder);


    /**
     * Find and run all init methods
     * - defined in relationship traits and generally to init ArrayCollection properties
     *
     * @throws \ReflectionException
     */
    protected function runInitMethods(): void
    {
        $reflectionClass = new \ts\Reflection\ReflectionClass(self::class);
        $methods         = $reflectionClass->getMethods(\ReflectionMethod::IS_PRIVATE);
        foreach ($methods as $method) {
            if ($method instanceof \ReflectionMethod) {
                $method = $method->getName();
            }
            if (\ts\stringContains($method, UsesPHPMetaDataInterface::METHOD_PREFIX_INIT)
                && \ts\stringStartsWith($method, UsesPHPMetaDataInterface::METHOD_PREFIX_INIT)
            ) {
                $this->$method();
            }
        }
    }
}
