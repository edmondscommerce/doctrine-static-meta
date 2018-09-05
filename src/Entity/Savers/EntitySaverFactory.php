<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Savers;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Proxy\Proxy;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;

class EntitySaverFactory
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var EntitySaver
     */
    protected $genericEntitySaver;
    /**
     * @var EntitySaver
     */
    protected $genericSaver;
    /**
     * @var NamespaceHelper
     */
    protected $namespaceHelper;

    public function __construct(
        EntityManagerInterface $entityManager,
        EntitySaver $genericSaver,
        NamespaceHelper $namespaceHelper
    ) {
        $this->entityManager   = $entityManager;
        $this->genericSaver    = $genericSaver;
        $this->namespaceHelper = $namespaceHelper;
    }

    /**
     * Gets the Entity Specific Saver if one is defined, otherwise the standard Entity Saver
     *
     * @param EntityInterface $entity
     *
     * @return EntitySaverInterface
     */
    public function getSaverForEntity(
        EntityInterface $entity
    ): EntitySaverInterface {
        $fqn = $this->getEntityNamespace($entity);

        return $this->getSaverForEntityFqn($fqn);
    }

    /**
     * It is possible to pass a proxy to the class which will trigger a fatal error due to autoloading problems.
     *
     * This will resolve the namespace to that of the entity, rather than the proxy. May need to update this to handle
     * other cases
     *
     * @param EntityInterface $entity
     *
     * @return string
     */
    private function getEntityNamespace(EntityInterface $entity): string
    {
        if ($entity instanceof Proxy) {
            $proxyFqn  = \get_class($entity);
            $namespace = $this->entityManager->getConfiguration()->getProxyNamespace();
            $marker    = \Doctrine\Common\Persistence\Proxy::MARKER;

            return str_replace($namespace . '\\' . $marker . '\\', '', $proxyFqn);
        }

        return $this->namespaceHelper->getObjectFqn($entity);
    }

    /**
     * @param string $entityFqn
     *
     * @return EntitySaverInterface
     */
    public function getSaverForEntityFqn(string $entityFqn): EntitySaverInterface
    {
        $saverFqn = $this->getSaverFqn($entityFqn);
        if (class_exists($saverFqn)) {
            return new $saverFqn($this->entityManager, $this->namespaceHelper);
        }

        return $this->genericSaver;
    }

    /**
     * Get the fully qualified name of the saver for the entity we are testing.
     *
     * @param string $entityFqn
     *
     * @return string
     */
    protected function getSaverFqn(
        string $entityFqn
    ): string {

        return \str_replace(
            'Entities',
            'Entity\\Savers',
            $entityFqn
        ) . 'Saver';
    }
}
