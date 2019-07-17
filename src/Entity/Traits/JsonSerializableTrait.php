<?php declare(strict_types=1);


namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Traits;


use EdmondsCommerce\DoctrineStaticMeta\DoctrineStaticMeta;
use Ramsey\Uuid\UuidInterface;

trait JsonSerializableTrait
{
    public function jsonSerialize(): array
    {
        $dsm         = static::getDoctrineStaticMeta();
        $toSerialize = [];
        $getters     = $dsm->getGetters();
        foreach ($getters as $getter) {
            $got = $this->$getter();
            if ($got instanceof UuidInterface) {
                $got = $got->toString();
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