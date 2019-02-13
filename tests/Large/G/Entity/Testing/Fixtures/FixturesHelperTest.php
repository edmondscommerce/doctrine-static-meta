<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\G\Entity\Testing\Fixtures;

use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Common\Collections\ArrayCollection;
use EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\DtoFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactoryInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Factories\UuidFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures\AbstractEntityFixtureLoader;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures\FixtureEntitiesModifierInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures\FixturesHelper;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Database;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Schema;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractLargeTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;
use Ramsey\Uuid\UuidInterface;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures\AbstractEntityFixtureLoader
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures\FixturesHelper
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class FixturesHelperTest extends AbstractLargeTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH .
                            self::TEST_TYPE_LARGE .
                            '/FixturesTest';

    private const ENTITY_WITHOUT_MODIFIER = self::TEST_ENTITIES_ROOT_NAMESPACE .
                                            TestCodeGenerator::TEST_ENTITY_PERSON;

    private const ENTITY_WITH_MODIFIER = self::TEST_ENTITIES_ROOT_NAMESPACE .
                                         TestCodeGenerator::TEST_ENTITY_PERSON;

    protected static $buildOnce = true;
    /**
     * @var FixturesHelper
     */
    private $helper;

    public function setup(): void
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getTestCodeGenerator()
                 ->copyTo(self::WORK_DIR, self::TEST_PROJECT_ROOT_NAMESPACE);
            $this->overrideCode();
            self::$built = true;
        }
        $this->setupCopiedWorkDirAndCreateDatabase();
        $this->recreateDtos();
        $cacheDir = $this->copiedWorkDir . '/cache';
        mkdir($cacheDir, 0777, true);
        $this->helper = new FixturesHelper(
            $this->getEntityManager(),
            $this->container->get(Database::class),
            $this->container->get(Schema::class),
            new FilesystemCache($cacheDir),
            $this->container->get(EntitySaverFactory::class),
            $this->getNamespaceHelper(),
            $this->getTestEntityGeneratorFactory(),
            $this->container
        );
    }

    private function overrideCode(): void
    {
        $personClass = <<<'PHP'
<?php declare(strict_types=1);

namespace My\Test\Project\Entities;
// phpcs:disable Generic.Files.LineLength.TooLong

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use My\Test\Project\Entity\Fields\Traits\BooleanFieldTrait;
use My\Test\Project\Entity\Fields\Traits\DatetimeFieldTrait;
use My\Test\Project\Entity\Fields\Traits\DecimalFieldTrait;
use My\Test\Project\Entity\Fields\Traits\FloatFieldTrait;
use My\Test\Project\Entity\Fields\Traits\IntegerFieldTrait;
use My\Test\Project\Entity\Fields\Traits\JsonFieldTrait;
use My\Test\Project\Entity\Fields\Traits\StringFieldTrait;
use My\Test\Project\Entity\Fields\Traits\TextFieldTrait;
use My\Test\Project\Entity\Interfaces\PersonInterface;
use My\Test\Project\Entity\Relations\Attributes\Address\Traits\HasAttributesAddress\HasAttributesAddressUnidirectionalManyToOne;
use My\Test\Project\Entity\Relations\Attributes\Email\Traits\HasRequiredAttributesEmails\HasRequiredAttributesEmailsOneToMany;
use My\Test\Project\Entity\Relations\Company\Director\Traits\HasCompanyDirector\HasCompanyDirectorInverseOneToOne;
use My\Test\Project\Entity\Relations\Large\Relation\Traits\HasLargeRelation\HasLargeRelationInverseOneToOne;

// phpcs:enable
class Person implements 
    PersonInterface
{
    /**
     * DSM Traits 
     */
    use DSM\Traits\UsesPHPMetaDataTrait;
    use DSM\Traits\ValidatedEntityTrait;
    use DSM\Traits\ImplementNotifyChangeTrackingPolicy;
    use DSM\Traits\AlwaysValidTrait;

    /**
     * Required Relations 
     */
    use HasRequiredAttributesEmailsOneToMany;

    /**
     * Relations 
     */
    use HasAttributesAddressUnidirectionalManyToOne;
    use HasCompanyDirectorInverseOneToOne;
    use HasLargeRelationInverseOneToOne;

    /**
     * DSM Fields 
     */
    use DSM\Fields\Traits\PrimaryKey\NonOrderedUuidFieldTrait;

    /**
     * Fields 
     */
    use StringFieldTrait;
    use DatetimeFieldTrait;
    use FloatFieldTrait;
    use DecimalFieldTrait;
    use IntegerFieldTrait;
    use TextFieldTrait;
    use BooleanFieldTrait;
    use JsonFieldTrait;
}
PHP;
        \ts\file_put_contents(self::WORK_DIR . '/src/Entities/Person.php', $personClass);

        $personFixture = <<<'PHP'
<?php declare(strict_types=1);

namespace My\Test\Project\Assets\Entity\Fixtures;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures\AbstractEntityFixtureLoader;
use My\Test\Project\Assets\Entity\Fixtures\Attributes\EmailFixture;

class PersonFixture extends AbstractEntityFixtureLoader implements DependentFixtureInterface
{
    public const REFERENCE_PREFIX = 'Person_';
    
    public const BULK_AMOUNT_TO_GENERATE = 2;

    public function getDependencies(): array
    {
        return [EmailFixture::class];
    }

    protected function loadBulk(): array
    {
        $entities = parent::loadBulk();
        $num      = 0;
        foreach ($entities as $person) {
            $collection = new ArrayCollection();
            $collection->add($this->getReference(EmailFixture::REFERENCE_PREFIX . $num++));
            $person->setAttributesEmails($collection);
        }

        return $entities;
    }
}
PHP;
        \ts\file_put_contents(self::WORK_DIR . '/tests/Assets/Entity/Fixtures/PersonFixture.php', $personFixture);

        $emailFixture = <<<'PHP'
<?php declare(strict_types=1);

namespace My\Test\Project\Assets\Entity\Fixtures\Attributes;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures\AbstractEntityFixtureLoader;

class EmailFixture extends AbstractEntityFixtureLoader
{
    public const REFERENCE_PREFIX = 'Email_';
    public const BULK_AMOUNT_TO_GENERATE = 2;
}

PHP;
        \ts\file_put_contents(self::WORK_DIR . '/tests/Assets/Entity/Fixtures/Attributes/EmailFixture.php',
                              $emailFixture);
    }

    /**
     * @test
     * @large
     */
    public function itLoadsAllTheFixturesWithRandomDataByDefault(): array
    {
        $this->helper->setCacheKey(__CLASS__ . '_unmodified');
        $fixture = $this->getUnmodifiedFixture();
        $this->helper->addFixture($fixture);
        $this->helper->createDb();
        $entityFqn   = $this->getCopiedFqn(self::ENTITY_WITHOUT_MODIFIER);
        $actual      = $this->getRepositoryFactory()
                            ->getRepository($entityFqn)
                            ->findAll();
        $actualCount = count($actual);
        self::assertSame($fixture::BULK_AMOUNT_TO_GENERATE, $actualCount);

        return $actual;
    }

    private function getUnmodifiedFixture(): AbstractEntityFixtureLoader
    {
        return $this->getFixture($this->getCopiedFqn(self::ENTITY_WITHOUT_MODIFIER));
    }

    private function getFixture(
        string $entityFqn,
        ?FixtureEntitiesModifierInterface $modifier = null
    ): AbstractEntityFixtureLoader {
        return $this->helper->createFixtureInstanceForEntityFqn($entityFqn, $modifier);
    }

    /**
     * @test
     * @large
     * @depends itLoadsAllTheFixturesWithRandomDataByDefault
     *
     * @param array $loadedFirstTime
     *
     * @return array
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function itUsesTheCacheTheSecondTime(array $loadedFirstTime): array
    {
        $this->getFileSystem()
             ->mirror(
                 $this->copiedWorkDir .
                 '/../FixturesHelperTest_ItLoadsAllTheFixturesWithRandomDataByDefault_/cache',
                 $this->copiedWorkDir . '/cache'
             );
        $this->helper->setCacheKey(__CLASS__ . '_unmodified');
        $fixture = $this->getUnmodifiedFixture();
        $this->helper->addFixture($fixture);
        $this->helper->createDb();
        self::assertTrue($this->helper->isLoadedFromCache());
        /**
         * @var EntityInterface[] $loadedSecondTime
         */
        $loadedSecondTime = $this->getRepositoryFactory()
                                 ->getRepository($this->getCopiedFqn(self::ENTITY_WITHOUT_MODIFIER))
                                 ->findAll();
        $actualCount      = count($loadedSecondTime);
        $expectedCount    = count($loadedFirstTime);
        self::assertSame($expectedCount, $actualCount);
        $first  = $this->getArrayKeyedByUuid($loadedFirstTime);
        $second = $this->getArrayKeyedByUuid($loadedSecondTime);
        foreach ($second as $secondId => $actualEntity) {
            self::assertArrayHasKey($secondId, $first, 'Failed finding UUID ' . $secondId . ' in first Entities');
            $expectedEntity = $first[$secondId];
            $expectedText   = $expectedEntity->getString();
            $actualText     = $actualEntity->getString();
            self::assertEquals($expectedText, $actualText, 'Cached Faker data does not match');
        }

        return $loadedSecondTime;
    }

    /**
     * @param array $entities
     *
     * @return EntityInterface[]
     * @return EntityInterface[]
     */
    private function getArrayKeyedByUuid(array $entities): array
    {
        $return = [];
        foreach ($entities as $entity) {
            $return[$entity->getId()->toString()] = $entity;
        }

        return $return;
    }

    /**
     * @test
     * @large
     * @depends itUsesTheCacheTheSecondTime
     *
     * @param array $loadedSecondTime
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function itCanBeConfiguredNotToLoadFromTheCache(array $loadedSecondTime): void
    {
        $this->getFileSystem()
             ->mirror(
                 $this->copiedWorkDir .
                 '/../FixturesHelperTest_ItUsesTheCacheTheSecondTime_/cache',
                 $this->copiedWorkDir . '/cache'
             );
        $this->helper->setCacheKey(__CLASS__ . '_unmodified');
        $fixture = $this->getUnmodifiedFixture();
        $this->helper->setLoadFromCache(false);
        $this->helper->addFixture($fixture);
        $this->helper->createDb();
        self::assertFalse($this->helper->isLoadedFromCache());
        /**
         * @var EntityInterface[] $loadedThirdTime
         */
        $loadedThirdTime = $this->getRepositoryFactory()
                                ->getRepository($this->getCopiedFqn(self::ENTITY_WITHOUT_MODIFIER))
                                ->findAll();
        $actualCount     = count($loadedThirdTime);
        $expectedCount   = count($loadedSecondTime);
        self::assertSame($expectedCount, $actualCount);
        $second = $this->getArrayKeyedByUuid($loadedSecondTime);
        foreach ($loadedThirdTime as $actualEntity) {
            self::assertArrayNotHasKey($actualEntity->getId()->toString(), $second);
        }
    }

    /**
     * @test
     * @large
     */
    public function itCanTakeAModifierToCustomiseTheFixtures(): void
    {
        $this->helper->setCacheKey(__CLASS__ . '_modified');
        $fixture = $this->getModifiedFixture();
        $this->helper->addFixture($fixture);
        $this->helper->createDb();
        /**
         * @var EntityInterface[] $actual
         */
        $actual      = $this->getRepositoryFactory()
                            ->getRepository($this->getCopiedFqn(self::ENTITY_WITH_MODIFIER))
                            ->findAll();
        $actualCount = count($actual);
        self::assertSame($fixture::BULK_AMOUNT_TO_GENERATE + 1, $actualCount);
        $foundStrings = [];
        foreach ($actual as $entity) {
            $foundStrings[$entity->getString()] = true;
        }
        $overwrittenString = 'This has been overridden';
        $createdString     = 'This has been created';
        self::assertArrayHasKey($overwrittenString, $foundStrings);
        self::assertArrayHasKey($createdString, $foundStrings);
    }

    private function getModifiedFixture(): AbstractEntityFixtureLoader
    {
        return $this->getFixture(
            $this->getCopiedFqn(self::ENTITY_WITH_MODIFIER),
            $this->getFixtureModifier()
        );
    }

    /**
     * @return FixtureEntitiesModifierInterface
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private function getFixtureModifier(): FixtureEntitiesModifierInterface
    {

        return new class(
            $this->getCopiedFqn(self::ENTITY_WITH_MODIFIER),
            $this->getEntityFactory(),
            $this->getEntityDtoFactory(),
            $this->getUuidFactory(),
            $this->getCopiedFqn(self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_EMAIL)
        )
            implements FixtureEntitiesModifierInterface
        {
            /**
             * @var string
             */
            protected $entityFqn;
            /**
             * @var EntityFactoryInterface
             */
            protected $factory;
            /**
             * @var array|EntityInterface[]
             */
            private $entities;
            /**
             * @var DtoFactory
             */
            private $dtoFactory;
            /**
             * @var UuidFactory
             */
            private $uuidFactory;
            /**
             * @var string
             */
            private $emailFqn;

            public function __construct(
                string $entityFqn,
                EntityFactoryInterface $factory,
                DtoFactory $dtoFactory,
                UuidFactory $uuidFactory,
                string $emailFqn
            ) {
                $this->entityFqn   = $entityFqn;
                $this->factory     = $factory;
                $this->dtoFactory  = $dtoFactory;
                $this->uuidFactory = $uuidFactory;
                $this->emailFqn    = $emailFqn;
            }

            /**
             * Update the entities array by reference
             *
             * @param array $entities
             */
            public function modifyEntities(array &$entities): void
            {
                $this->entities = &$entities;
                $this->updateFirstEntity();
                $this->addAnotherEntity();
            }

            private function updateFirstEntity(): void
            {
                $firstEntity = current($this->entities);
                $firstEntity->update(
                    new class($this->entityFqn, $firstEntity->getId())
                        implements DataTransferObjectInterface
                    {
                        /**
                         * @var string
                         */
                        private static $entityFqn;
                        /**
                         * @var UuidInterface
                         */
                        private $id;

                        public function __construct(string $entityFqn, UuidInterface $id)
                        {
                            self::$entityFqn = $entityFqn;
                            $this->id        = $id;
                        }

                        public function getString(): string
                        {
                            return 'This has been overridden';
                        }

                        public static function getEntityFqn(): string
                        {
                            return self::$entityFqn;
                        }

                        public function getId(): UuidInterface
                        {
                            return $this->id;
                        }
                    }
                );
            }

            private function addAnotherEntity(): void
            {
                $address = $this->factory->create($this->emailFqn);
                $entity  = $this->factory->create(
                    $this->entityFqn,
                    new class($this->entityFqn, $this->uuidFactory, $address) implements DataTransferObjectInterface
                    {
                        /**
                         * @var string
                         */
                        private static $entityFqn;
                        /**
                         * @var \Ramsey\Uuid\UuidInterface
                         */
                        private $id;
                        /**
                         * @var EntityInterface
                         */
                        private $email;

                        public function __construct(string $entityFqn, UuidFactory $factory, EntityInterface $email)
                        {
                            self::$entityFqn = $entityFqn;
                            $this->id        = $factory->getOrderedTimeUuid();
                            $this->email     = $email;
                        }

                        public function getString(): string
                        {
                            return 'This has been created';
                        }

                        public static function getEntityFqn(): string
                        {
                            return self::$entityFqn;
                        }

                        public function getId(): UuidInterface
                        {
                            return $this->id;
                        }

                        public function getAttributesEmails(): ArrayCollection
                        {
                            $collection = new ArrayCollection();
                            $collection->add($this->email);

                            return $collection;
                        }
                    }
                );

                $this->entities[] = $entity;
            }
        };
    }
}
