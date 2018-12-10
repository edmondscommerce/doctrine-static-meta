<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\D\Entity\Savers;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\EmailAddressFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractLargeTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\GetGeneratedCodeContainerTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;

/**
 * @large
 */
class EntityUpserterLargeTest extends AbstractLargeTest
{
    use GetGeneratedCodeContainerTrait;

    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/EntityUpserterLargeTest';

    private const TEST_ENTITY_FQN = self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_EMAIL;

    protected static $buildOnce = true;
    /** @var string */
    private $entityFqn;
    /** @var NamespaceHelper */
    private $namespaceHelper;

    public function setUp()
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getTestCodeGenerator()
                 ->copyTo(self::WORK_DIR);
            self::$built = true;
        }
        $this->setupCopiedWorkDirAndCreateDatabase();
        $this->entityFqn       = $this->getCopiedFqn(self::TEST_ENTITY_FQN);
        $this->namespaceHelper = self::$containerStaticRef->get(NamespaceHelper::class);
    }

    /**
     * @test
     */
    public function itCanCreateADtoForAnEntityThatDoesNotExist(): void
    {
        $upserter     = $this->getUpserter();
        $className    = $this->namespaceHelper->getEntityUpserterFqnFromEntityFqn($this->entityFqn);
        $unknownEmail = 'not.real@example.com';
        $this->assertInstanceOf($className, $upserter);
        $dto = $upserter->getUpsertDtoByProperty(EmailAddressFieldInterface::PROP_EMAIL_ADDRESS, $unknownEmail);
        self::assertInstanceOf(DataTransferObjectInterface::class, $dto);
        self::assertNull($dto->getJson());
        self::assertSame($unknownEmail, $dto->getEmailAddress());
    }

    /**
     * @test
     */
    public function itCanCreateADtoForAnExistingEntity(): void
    {
        $expectedEmail = 'just.created@example.com';
        $expectedId    = $this->createAndSaveEntity($expectedEmail);
        $upserter      = $this->getUpserter();
        $dto           = $upserter->getUpsertDtoByProperty(
            EmailAddressFieldInterface::PROP_EMAIL_ADDRESS,
            $expectedEmail
        );
        self::assertSame($expectedEmail, $dto->getEmailAddress());
        self::assertSame($expectedId, $dto->getId()->toString());
    }

    /**
     * @test
     */
    public function itCanCreateADtoForAnExistingEntityUsingMultipleProperties(): void
    {
        $expectedEmail  = 'just.created@example.com';
        $expectedString = 'This is a test string value';
        $expectedId     = $this->createAndSaveEntity($expectedEmail, $expectedString);
        $upserter       = $this->getUpserter();
        $dto            = $upserter->getUpsertDtoByProperties(
            [
                EmailAddressFieldInterface::PROP_EMAIL_ADDRESS => $expectedEmail,
                'string' => $expectedString
            ]
        );
        self::assertSame($expectedEmail, $dto->getEmailAddress());
        self::assertSame($expectedString, $dto->getString());
        self::assertSame($expectedId, $dto->getId()->toString());
    }

    /**
     * @test
     */
    public function itCanPersistADtoForANewEntity(): void
    {
        $expectedEmail = 'an.address.that.does.not.exist.yet@example.com';
        $upserter      = $this->getUpserter();
        $dto           = $upserter->getUpsertDtoByProperty(
            EmailAddressFieldInterface::PROP_EMAIL_ADDRESS,
            $expectedEmail
        );
        $dto->setEmailAddress($expectedEmail);
        $upserter->persistUpsertDto($dto);
        $repository    = $this->getRepository();
        $updatedEntity = $repository->getOneBy([EmailAddressFieldInterface::PROP_EMAIL_ADDRESS => $expectedEmail]);
        self::assertSame($expectedEmail, $updatedEntity->getEmailAddress());
    }

    /**
     * @test
     */
    public function itCanPersistADtoForAnExistingDto(): void
    {
        $oldEmailAddress = 'this.will.be.changed@example.com';
        $expectedId      = $this->createAndSaveEntity($oldEmailAddress);
        $upserter        = $this->getUpserter();
        $dto             = $upserter->getUpsertDtoByProperty(
            EmailAddressFieldInterface::PROP_EMAIL_ADDRESS,
            $oldEmailAddress
        );
        $newEmailAddress = 'has.been.updated@example.com';
        $dto->setEmailAddress($newEmailAddress);
        $upserter->persistUpsertDto($dto);
        $repository    = $this->getRepository();
        $updatedEntity = $repository->getOneBy([EmailAddressFieldInterface::PROP_EMAIL_ADDRESS => $newEmailAddress]);
        self::assertSame($newEmailAddress, $updatedEntity->getEmailAddress());
        self::assertSame($expectedId, $updatedEntity->getId()->toString());
    }

    private function createAndSaveEntity(string $emailAddress, ?string $stringValue = null): string
    {
        $entity     = $this->createEntity($this->entityFqn);
        $dtoFactory = $this->getDtoFactory();
        $updateDto  = $dtoFactory->createDtoFromEmail($entity)->setEmailAddress($emailAddress);
        if ($stringValue !== null) {
            $updateDto->setString($stringValue);
        }
        $entity->update($updateDto);
        $saver = $this->getEntitySaver();
        $saver->save($entity);

        return $entity->getId()->toString();
    }

    private function getDtoFactory()
    {
        $dtoFactory = $this->namespaceHelper->getDtoFactoryFqnFromEntityFqn($this->entityFqn);

        return $this->getGeneratedClass($dtoFactory);
    }

    private function getRepository()
    {
        $repository = $this->namespaceHelper->getRepositoryqnFromEntityFqn($this->entityFqn);

        return $this->getGeneratedClass($repository);
    }

    private function getUpserter()
    {
        $class = $this->namespaceHelper->getEntityUpserterFqnFromEntityFqn($this->entityFqn);

        return $this->getGeneratedClass($class);
    }
}
