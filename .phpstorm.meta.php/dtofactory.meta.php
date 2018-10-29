<?php declare(strict_types=1);

namespace PHPSTORM_META {

    override(
        \EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\DtoFactory::createEmptyDtoFromEntityFqn(0),
        map(
            [
                '' => '@',
            ]
        )
    );

    override(
        \EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\DtoFactory::createDtoFromEntity(0),
        map(
            [
                '' => type(0),
            ]
        )
    );
}