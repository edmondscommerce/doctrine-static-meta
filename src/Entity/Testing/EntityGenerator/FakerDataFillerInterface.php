<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\DoctrineStaticMeta;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;

interface FakerDataFillerInterface
{
    public function __construct(
        FakerDataFillerFactory $fakerDataFillerFactory,
        DoctrineStaticMeta $testedEntityDsm,
        NamespaceHelper $namespaceHelper,
        array $fakerDataProviderClasses,
        ?float $seed = null
    );

    public function updateDtoWithFakeData(DataTransferObjectInterface $dto): void;

    /**
     * @param DataTransferObjectInterface $dto
     * @param bool                        $isRootDto
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function update(DataTransferObjectInterface $dto, $isRootDto = false): void;
}
