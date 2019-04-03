<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\AbstractEntityUpdateDto;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractLargeTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;

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
    public function youCanUpdateWithAnEmptyCollection(): void
    {
        $dto = new class($this->entityFqn, $this->entity->getId()) extends AbstractEntityUpdateDto
        {
            public function getAttributesEmails(): ArrayCollection
            {
                return new ArrayCollection();
            }
        };
        $this->entity->update($dto);
        $this->saver->save($this->entity);
        $this->getEntityManager()->clear();
        $loaded   = $this->getRepositoryFactory()->getRepository($this->entityFqn)->find($this->entity->getId());
        $expected = 0;
        $actual   = $loaded->getAttributesEmails()->count();
        self::assertSame($expected, $actual);
    }
}
