<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Proxy\Proxy;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use ReflectionException;
use RuntimeException;
use TypeError;
use function get_class;
use function is_numeric;
use function lcfirst;
use function preg_replace;

/**
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
class EntityDebugDumper
{
    /**
     * @param EntityInterface        $entity
     * @param EntityManagerInterface $entityManager
     * @param int                    $level
     *
     * @return string
     * @throws ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function dump(EntityInterface $entity, ?EntityManagerInterface $entityManager = null, int $level = 0): string
    {
        $dump          = [];
        $fieldMappings = [];
        if (null !== $entityManager) {
            $metaData      = $entityManager->getClassMetadata(get_class($entity));
            $fieldMappings = $metaData->fieldMappings;
        }
        foreach ($entity::getDoctrineStaticMeta()->getGetters() as $getter) {
            try {
                $got = $entity->$getter();
            } catch (TypeError $e) {
                $got = '( *TypeError*: ' . $e->getMessage() . ' )';
            }
            $fieldName = lcfirst(preg_replace('%^(get|is)%', '', $getter));
            if (is_numeric($got)
                || (isset($fieldMappings[$fieldName]) && 'decimal' === $fieldMappings[$fieldName]['type'])
            ) {
                $dump[$getter] = (float)$got;
                continue;
            }
            if ($got instanceof Proxy) {
                $dump[$getter] = 'Proxy class ';
                continue;
            }
            if ($got instanceof EntityInterface) {
                if ($level === 2) {
                    $dump[$getter] = '(max depth of 2 reached)';
                    continue;
                }
                $dump[$getter] = $this->dump($got, $entityManager, ++$level);
                continue;
            }
            if ($got instanceof Collection) {
                $dump[$getter] = [];
                foreach ($got as $item) {
                    if ($item instanceof EntityInterface) {
                        $dump[$getter][] = get_class($item) . ': ' . $item->getId();
                        continue;
                    }
                    throw new RuntimeException('Got unexpected object ' .
                                                get_class($got) .
                                                ' in collection from ' .
                                                $getter);
                }
                continue;
            }
            if (method_exists($got, '__toString')) {
                $dump[$getter] = $got->__toString();
                continue;
            }
            $dump[$getter] = Debug::export($got, 2);
        }

        return (string)print_r($dump, true);
    }
}
