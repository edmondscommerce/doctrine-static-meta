<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;

class EntityDebugDumper
{
    /**
     * @param EntityInterface        $entity
     * @param EntityManagerInterface $entityManager
     * @param int                    $level
     *
     * @return string
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function dump(EntityInterface $entity, ?EntityManagerInterface $entityManager = null, int $level = 0): string
    {
        $dump          = [];
        $fieldMappings = [];
        if (null !== $entityManager) {
            $metaData      = $entityManager->getClassMetadata(\get_class($entity));
            $fieldMappings = $metaData->fieldMappings;
        }
        foreach ($entity::getDoctrineStaticMeta()->getGetters() as $getter) {
            $got       = $entity->$getter();
            $fieldName = \lcfirst(\preg_replace('%^(get|is)%', '', $getter));
            if (\is_numeric($got)
                || (isset($fieldMappings[$fieldName]) && 'decimal' === $fieldMappings[$fieldName]['type'])
            ) {
                $dump[$getter] = (float)$got;
                continue;
            }
            if ($got instanceof \Doctrine\ORM\Proxy\Proxy) {
                $dump[$getter] = 'Proxy class ';
                continue;
            }
            if (method_exists($got, '__toString')) {
                $dump[$getter] = $got->__toString();
                continue;
            }
            if (\is_object($got) && $got instanceof EntityInterface) {
                if ($level === 2) {
                    $dump[$getter] = '(max depth of 2 reached)';
                    continue;
                }
                $dump[$getter] = $this->dump($got, $entityManager, ++$level);
                continue;
            }
            $dump[$getter] = Debug::export($got, 2);
        }

        return (string)print_r($dump, true);
    }
}
