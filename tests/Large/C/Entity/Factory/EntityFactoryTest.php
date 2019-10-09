<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Factory;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityDependencyInjector;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactoryInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\EmailAddressFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\IsbnFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ValidationException;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactory
 * @large
 */
class EntityFactoryTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/EntityFactoryTest';

    private const TEST_ENTITY_FQN = self::TEST_ENTITIES_ROOT_NAMESPACE .
                                    TestCodeGenerator::TEST_ENTITY_ALL_ARCHETYPE_FIELDS;

    private const TEST_VALUES = [
        IsbnFieldInterface::PROP_ISBN                  => '978-3-16-148410-0',
        EmailAddressFieldInterface::PROP_EMAIL_ADDRESS => 'test@test.com',
    ];
    protected static $buildOnce = true;
    private $entityFqn;
    /**
     * @var EntityFactoryInterface
     */
    private $factory;

    public function setup()
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getTestCodeGenerator()
                 ->copyTo(self::WORK_DIR);
            self::$built = true;
        }
        $this->setupCopiedWorkDir();
        $this->entityFqn = $this->getCopiedFqn(self::TEST_ENTITY_FQN);
        $this->factory   = new EntityFactory(
            $this->getNamespaceHelper(),
            $this->container->get(EntityDependencyInjector::class),
            $this->getEntityDtoFactory()
        );
        $this->factory->setEntityManager($this->getEntityManager());
    }


    /**
     * @test
     */
    public function itCanCreateAnEmptyEntity(): void
    {
        $entity = $this->factory->create($this->entityFqn, $this->getValidDtoForTestEntity());
        self::assertInstanceOf($this->entityFqn, $entity);
    }

    private function getValidDtoForTestEntity(): DataTransferObjectInterface
    {
        return $this->getEntityDtoFactory()
                    ->createEmptyDtoFromEntityFqn($this->entityFqn)
                    ->setShortIndexedRequiredString('required')
                    ->setEmailAddress(self::TEST_VALUES[EmailAddressFieldInterface::PROP_EMAIL_ADDRESS])
                    ->setIsbn(self::TEST_VALUES[IsbnFieldInterface::PROP_ISBN]);
    }

    /**
     * @test
     */
    public function itThrowsAnExceptionIfThereIsAnInvalidProperty(): void
    {
        $dto = $this->getValidDtoForTestEntity();
        $dto->setEmailAddress('invalid');
        $this->expectException(ValidationException::class);
        $this->factory->create(
            $this->entityFqn,
            $dto
        );
    }

    /**
     * @test
     */
    public function itCanCreateAnEntityWithValues(): void
    {

        $entity = $this->factory->create($this->entityFqn, $this->getValidDtoForTestEntity());

        self::assertSame($entity->getIsbn(), self::TEST_VALUES[IsbnFieldInterface::PROP_ISBN]);

        self::assertSame($entity->getEmailAddress(), self::TEST_VALUES[EmailAddressFieldInterface::PROP_EMAIL_ADDRESS]);
    }

    /**
     * @test
     */
    public function itCanCreateAnEntitySpecificFactoryAndCanCreateThatEntity(): void
    {
        $entityFactory    = $this->factory->createFactoryForEntity($this->entityFqn);
        $entityFactoryFqn = $this->getNamespaceHelper()->getFactoryFqnFromEntityFqn($this->entityFqn);
        self::assertInstanceOf($entityFactoryFqn, $entityFactory);
        self::assertInstanceOf(
            $this->entityFqn,
            $entityFactory->create(
                $this->getValidDtoForTestEntity()
            )
        );
    }

    /**
     * @test
     */
    public function itCanCreateAnEntityWithRequiredRelationsUsingNestedDtos(): void
    {
        $attributesAddressFqn =
            $this->getCopiedFqn(self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_ATTRIBUTES_ADDRESS);

        $companyDirectorFqn =
            $this->getCopiedFqn(self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_DIRECTOR);

        $personFqn =
            $this->getCopiedFqn(self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_PERSON);

        $emailFqn =
            $this->getCopiedFqn(self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_EMAIL);

        $companyFqn =
            $this->getCopiedFqn(self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_COMPANY);

        $personDto = $this->getEntityDtoFactory()
                          ->createEmptyDtoFromEntityFqn($personFqn);
        $personDto->getAttributesEmails()->add(
            $this->getEntityDtoFactory()
                 ->createEmptyDtoFromEntityFqn($emailFqn)
                 ->setEmailAddress('person@mail.com')
        );

        $companyDto = $this->getEntityDtoFactory()
                           ->createEmptyDtoFromEntityFqn($companyFqn);

        $companyDto->getCompanyDirectors()
                   ->add(
                       $this->getEntityDtoFactory()
                            ->createEmptyDtoFromEntityFqn($companyDirectorFqn)
                            ->setPersonDto($personDto)
                   );

        $companyDto->getAttributesAddresses()
                   ->add(
                       $this->getEntityDtoFactory()
                            ->createEmptyDtoFromEntityFqn($attributesAddressFqn)
                   );

        $companyDto->getAttributesEmails()
                   ->add(
                       $this->getEntityDtoFactory()
                            ->createEmptyDtoFromEntityFqn($emailFqn)
                            ->setEmailAddress('company@mail.com')
                   );

        $company = $this->factory->create($companyFqn, $companyDto);
        self::assertInstanceOf($companyFqn, $company);
        self::assertInstanceOf($companyDirectorFqn, $company->getCompanyDirectors()->first());
        self::assertSame($company, $company->getCompanyDirectors()->first()->getCompanies()->first());
    }
}
