<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Savers;

use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use RuntimeException;

abstract class AbstractBulkProcess
{
    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $entityManager;

    protected array $entitiesToSave = [];

    protected int $chunkSize = 1000;

    /**
     * @var float
     */
    protected float $secondsToPauseBetweenSaves = 0.0;

    /**
     * @var bool
     */
    private $gcWasEnabled;

    private bool $started = false;
    private bool $ended   = false;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->gcWasEnabled  = gc_enabled();
    }

    public function __destruct()
    {
        if (true === $this->started && false === $this->ended) {
            if (!$this->entityManager->isOpen()) {
                throw new RuntimeException('Error in ' . __METHOD__ . ': Entity Manager has been closed');
            }
            $this->endBulkProcess();
        }
    }

    public function endBulkProcess(): void
    {
        $this->started = false;
        $this->ended   = true;

        if ([] !== $this->entitiesToSave) {
            $this->doSave();
            $this->freeResources();
        }
        if (false === $this->gcWasEnabled) {
            return;
        }
        gc_enable();
    }

    abstract protected function doSave(): void;

    protected function freeResources()
    {
        gc_enable();
        foreach ($this->entitiesToSave as $entity) {
            $this->entityManager->detach($entity);
        }
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

    protected function bulkSaveIfChunkBigEnough(): void
    {
        $size = count($this->entitiesToSave);
        if ($size >= $this->chunkSize) {
            $this->doSave();
            $this->freeResources();
            $this->pauseBetweenSaves();
        }
    }

    /**
     * If configured, we will pause between starting another round of saves
     */
    private function pauseBetweenSaves(): void
    {
        if (0 >= $this->secondsToPauseBetweenSaves) {
            return;
        }
        usleep((int)$this->secondsToPauseBetweenSaves * 1000000);
    }

    /**
     * This will prevent any notification on changed properties
     *
     * @param array|EntityInterface[] $entities
     *
     * @return $this
     */
    public function prepareEntitiesForBulkUpdate(array $entities): self
    {
        foreach ($entities as $entity) {
            $entity->removePropertyChangedListeners();
        }

        return $this;
    }

    public function addEntitiesToSave(array $entities)
    {
        $entitiesToSaveBackup = $this->entitiesToSave;
        $chunks               = array_chunk($entities, $this->chunkSize, true);
        foreach ($chunks as $chunk) {
            $this->entitiesToSave = $chunk;
            $this->bulkSaveIfChunkBigEnough();
        }
        $this->entitiesToSave = array_merge($this->entitiesToSave, $entitiesToSaveBackup);
        $this->bulkSaveIfChunkBigEnough();
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
     * @return $this
     */
    public function setChunkSize(int $chunkSize): self
    {
        $this->chunkSize = $chunkSize;

        return $this;
    }

    /**
     * @param float $secondsToPauseBetweenSaves
     */
    public function setSecondsToPauseBetweenSaves(float $secondsToPauseBetweenSaves): void
    {
        $this->secondsToPauseBetweenSaves = $secondsToPauseBetweenSaves;
    }
}
