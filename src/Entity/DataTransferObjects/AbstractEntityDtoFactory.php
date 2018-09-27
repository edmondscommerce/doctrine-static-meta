<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects;

abstract class AbstractEntityDtoFactory implements DtoFactoryInterface
{
    /**
     * @var DtoFactory
     */
    protected $dtoFactory;

    public function __construct(DtoFactory $dtoFactory)
    {
        $this->dtoFactory = $dtoFactory;
    }
}