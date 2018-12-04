<?php
/**
 * Created by PhpStorm.
 * User: ec
 * Date: 04/12/18
 * Time: 16:19
 */

namespace TemplateNamespace\Entity\Savers;


use TemplateNamespace\Entity\DataTransferObjects\TemplateEntityDto;
use TemplateNamespace\Entity\Factories\TemplateEntityDtoFactory;
use TemplateNamespace\Entity\Factories\TemplateEntityFactory;
use TemplateNamespace\Entity\Interfaces\TemplateEntityInterface;
use TemplateNamespace\Entity\Repositories\TemplateEntityRepository;

class TemplateEntityUpserter
{
    /**
     * @var TemplateEntityRepository
     */
    private $repository;
    /**
     * @var TemplateEntityDtoFactory
     */
    private $dtoFactory;
    /**
     * @var TemplateEntityFactory
     */
    private $entityFactory;
    /**
     * @var TemplateEntitySaver
     */
    private $saver;

    public function __construct(
        TemplateEntityRepository $repository,
        TemplateEntityDtoFactory $dtoFactory,
        TemplateEntityFactory $entityFactory,
        TemplateEntitySaver $saver
    ) {
        $this->repository = $repository;
        $this->dtoFactory = $dtoFactory;
        $this->entityFactory = $entityFactory;
        $this->saver = $saver;
    }

    public function getUpsertDtoByCriteria(array $criteria): TemplateEntityDto
    {

    }

    public function persistUpsertDto(TemplateEntityDto $dto): TemplateEntityInterface
    {
        
    }

    private function getDtoByCriteria(array $criteria): TemplateEntityDto
    {

    }
}