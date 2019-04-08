<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\AbstractEntityUpdateDto;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractLargeTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;
use Ramsey\Uuid\UuidInterface;

/**
 * @large
 */
class ImplementNotifyChangeTrackingPolicyTest extends AbstractLargeTest
{
    public const  WORK_DIR   = self::VAR_PATH .
                               '/' .
                               self::TEST_TYPE_LARGE .
                               '/ImplementNotifyChangeTrackingPolicyTest';
    private const ENTITY_FQN = self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_PERSON;
    protected static $buildOnce = true;
    private          $entity;
    /**
     * @var string
     */
    private $entityFqn;
    /**
     * @var EntitySaverInterface
     */
    private $saver;
    /**
     * @var \EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\TestEntityGenerator
     */
    private $testEntityGenerator;

    public function setup()
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getTestCodeGenerator()
                 ->copyTo(self::WORK_DIR);
            self::$built = true;
        }
        $this->setupCopiedWorkDirAndCreateDatabase();
        $this->entityFqn           = $this->getCopiedFqn(self::ENTITY_FQN);
        $this->saver               = $this->getEntitySaver();
        $this->testEntityGenerator = $this->getTestEntityGeneratorFactory()
                                          ->createForEntityFqn($this->entityFqn);
        $this->entity              = $this->testEntityGenerator->generateEntity();
        $this->testEntityGenerator->addAssociationEntities($this->entity);
        $this->saver->save($this->entity);
    }

    /**
     * @test
     * @throws DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function youCanRemoveItemsFromACollection(): void
    {
        /**
         * @var Collection $attributesEmails
         */
        $attributesEmails = $this->entity->getAttributesEmails();
        $attributesEmails->removeElement($attributesEmails->last());
        $dto = $this->getDto($attributesEmails);
        $this->entity->update($dto);
        $this->saver->save($this->entity);
        $this->getEntityManager()->clear();
        $loaded   = $this->getRepositoryFactory()->getRepository($this->entityFqn)->find($this->entity->getId());
        $expected = $attributesEmails->count();
        $actual   = $loaded->getAttributesEmails()->count();
        self::assertSame($expected, $actual);
    }

    private function getDto(Collection $attributesEmails)
    {
        return new class($this->entityFqn, $this->entity->getId(), $attributesEmails) extends AbstractEntityUpdateDto
        {
            private $attributesEmails;

            public function __construct(string $entityFqn, UuidInterface $id, Collection $attributesEmails)
            {
                $this->attributesEmails = $attributesEmails;
                parent::__construct($entityFqn, $id);
            }

            public function getAttributesEmails(): Collection
            {
                return $this->attributesEmails;
            }
        };
    }

    /**
     * @test
     * @throws DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function youCanAddItemsToACollection(): void
    {
        /**
         * @var Collection $attributesEmails
         */
        $attributesEmails = $this->entity->getAttributesEmails();
        $attributesEmails->add($this->getNewAttributeEmail());
        $dto = $this->getDto($attributesEmails);
        $this->entity->update($dto);
        $this->saver->save($this->entity);
        $this->getEntityManager()->clear();
        $loaded   = $this->getRepositoryFactory()->getRepository($this->entityFqn)->find($this->entity->getId());
        $expected = $attributesEmails->count();
        $actual   = $loaded->getAttributesEmails()->count();
        self::assertSame($expected, $actual);
    }

    private function getNewAttributeEmail(): EntityInterface
    {
        $attributesEmailsFqn = $this->getCopiedFqn(
            self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_EMAIL
        );

        return $this->getTestEntityGeneratorFactory()->createForEntityFqn($attributesEmailsFqn)->generateEntity();
    }

    /**
     * @test
     * @throws DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function youCanUpdateWithAnEmptyCollection(): void
    {
        $dto = $this->getDto(new ArrayCollection());
        $this->entity->update($dto);
        $this->saver->save($this->entity);
        $this->getEntityManager()->clear();
        $loaded   = $this->getRepositoryFactory()->getRepository($this->entityFqn)->find($this->entity->getId());
        $expected = 0;
        $actual   = $loaded->getAttributesEmails()->count();
        self::assertSame($expected, $actual);
    }
}
