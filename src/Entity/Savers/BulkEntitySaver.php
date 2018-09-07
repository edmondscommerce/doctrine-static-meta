<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Savers;

use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;

class BulkEntitySaver
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    private $entitiesToSave = [];

    private $chunkSize = 1000;

    /**
     * @var bool
     */
    private $gcWasEnabled;

    private $started = false;
    private $ended   = false;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->gcWasEnabled  = gc_enabled();
    }

    public function __destruct()
    {
        if (true === $this->started && false === $this->ended) {
            $this->endBulkProcess();
        }
    }

    public function endBulkProcess(): void
    {
        $this->started = false;
        $this->ended   = true;
        if ([] !== $this->entitiesToSave) {
            $this->doSave();
        }
        if (false === $this->gcWasEnabled) {
            return;
        }
        gc_enable();
    }

    private function doSave()
    {
        foreach ($this->entitiesToSave as $entity) {
            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();
        $this->freeResources();
    }

    private function freeResources()
    {
        gc_enable();
        $this->entitiesToSave = [];
        gc_collect_cycles();
        gc_disable();
    }

    public function addEntityToSave(EntityInterface $entity)
    {
        if (false === $this->started) {
            $this->startBulkProcess();
        }
        $this->entitiesToSave[] = $entity;
        $this->bulkSaveIfChunkBigEnough();
    }

    public function startBulkProcess(): self
    {
        gc_disable();
        $this->started = true;
        $this->ended   = false;

        return $this;
    }

    private function bulkSaveIfChunkBigEnough()
    {
        end($this->entitiesToSave);
        $key  = key($this->entitiesToSave);
        $size = $key + 1;
        if (($size % $this->chunkSize) === 0) {
            $this->doSave();
        }
    }

    /**
     * @return int
     */
    public function getChunkSize(): int
    {
        return $this->chunkSize;
    }

    /**
     * @param int $chunkSize
     *
     * @return BulkEntitySaver
     */
    public function setChunkSize(int $chunkSize): BulkEntitySaver
    {
        $this->chunkSize = $chunkSize;

        return $this;
    }
}
