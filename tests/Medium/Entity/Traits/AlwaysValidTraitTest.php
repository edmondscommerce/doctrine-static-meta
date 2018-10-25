<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\Entity\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\AbstractAnonymousUuidDto;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ValidationException;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;

/**
 * @medium
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Traits\AlwaysValidTrait
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class AlwaysValidTraitTest extends AbstractTest
{
    public const WORK_DIR = self::VAR_PATH . '/' . self::TEST_TYPE_MEDIUM . '/AlwaysValidTraitTest';

    protected static $buildOnce = true;

    public function setup()
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getTestCodeGenerator()
                 ->copyTo(self::WORK_DIR);
            self::$built = true;
        }
        $this->setupCopiedWorkDir();
    }

    /**
     * @test
     */
    public function mustIncludeRequiredRelationsWhenCreating(): void
    {
        $companyFqn = $this->getCopiedFqn(
            self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_COMPANY
        );
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Found 3 errors validating');
        $this->getEntityFactory()->create(
            $companyFqn,
            new class($companyFqn, $this->getUuidFactory()) extends AbstractAnonymousUuidDto
            {
            }
        );
    }

    /**
     * @test
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function nestedDtosMustBeValid(): void
    {
        $companyFqn         = $this->getCopiedFqn(
            self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_COMPANY
        );
        $companyDirectorFqn = $this->getCopiedFqn(
            self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_DIRECTOR
        );

        $companyDto         = $this->getEntityDtoFactory()->createEmptyDtoFromEntityFqn($companyFqn);
        $invalidCollection  = new ArrayCollection();
        $invalidDirectorDto = new class($companyDirectorFqn, $this->getUuidFactory()) extends AbstractAnonymousUuidDto
        {

        };
        $invalidCollection->add($invalidDirectorDto);
        $companyDto->setCompanyDirectors($invalidCollection);
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Found 2 errors validating');
        $this->getEntityFactory()->create(
            $companyFqn,
            $companyDto
        );
    }

    /**
     * @test
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function canCreateValidEntityWithRequiredRelations(): void
    {
        $companyFqn = $this->getCopiedFqn(
            self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_COMPANY
        );

        $companyDto = $this->getEntityDtoFactory()->createEmptyDtoFromEntityFqn($companyFqn);

        $emailFqn = $this->getCopiedFqn(
            self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_EMAIL
        );
        $email    = $this->getEntityFactory()->create($emailFqn);
        $companyDto->getAttributesEmails()->add($email);

        $personFqn = $this->getCopiedFqn(self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_PERSON);
        $personDto = $this->getEntityDtoFactory()->createEmptyDtoFromEntityFqn($personFqn);
        $personDto->getAttributesEmails()->add($email);

        $companyDirectorFqn = $this->getCopiedFqn(
            self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_DIRECTOR
        );
        $companyDirectorDto = $this->getEntityDtoFactory()->createEmptyDtoFromEntityFqn($companyDirectorFqn);
        $companyDirectorDto->setPersonDto($personDto);
        $companyDirectorDto->setCompanies(new ArrayCollection([$companyDto]));
        $companyDto->getCompanyDirectors()->add($companyDirectorDto);

        $addressFqn = $this->getCopiedFqn(
            self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_ATTRIBUTES_ADDRESS
        );
        $companyDto->getAttributesAddresses()->add(
            $this->getEntityDtoFactory()->createEmptyDtoFromEntityFqn($addressFqn)
        );

        $company = $this->getEntityFactory()->create(
            $companyFqn,
            $companyDto
        );

        self::assertInstanceOf($companyFqn, $company);
        self::assertInstanceOf($emailFqn, $company->getAttributesEmails()->first());
        self::assertInstanceOf($companyDirectorFqn, $company->getCompanyDirectors()->first());
        self::assertInstanceOf($personFqn, $company->getCompanyDirectors()->first()->getPerson());
        self::assertInstanceOf($addressFqn, $company->getAttributesAddresses()->first());
    }
}
