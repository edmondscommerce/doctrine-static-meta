<?php declare(strict_types=1);


namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Traits;


use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Proxy\Proxy;
use EdmondsCommerce\DoctrineStaticMeta\DoctrineStaticMeta;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use Ramsey\Uuid\UuidInterface;

trait JsonSerializableTrait
{
    public function jsonSerialize(): array
    {
        $dsm         = static::getDoctrineStaticMeta();
        $toSerialize = [];
        $getters     = $dsm->getGetters();
        foreach ($getters as $getter) {
            /** @var mixed $got */
            $got = $this->$getter();
            if ($got instanceof EntityInterface) {
                continue;
            }
            if ($got instanceof Collection) {
                continue;
            }
            if ($got instanceof Proxy) {
                continue;
            }
            if ($got instanceof UuidInterface) {
                $got = $got->toString();
            }
            if (method_exists($got, '__toString')) {
                $got = (string)$got;
            }
            if (false === is_scalar($got)) {
                continue;
            }
            $property               = $dsm->getPropertyNameFromGetterName($getter);
            $toSerialize[$property] = $got;
        }

        return $toSerialize;
    }

    abstract public static function getDoctrineStaticMeta(): DoctrineStaticMeta;


}