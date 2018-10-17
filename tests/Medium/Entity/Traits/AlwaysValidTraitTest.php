<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\Entity\Traits;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ValidationException;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;
use Ramsey\Uuid\Uuid;

/**
 * @medium
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Traits\AlwaysValidTrait
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
    public function mustIncludeRequiredRelationsWhenCreating()
    {
        $companyFqn = $this->getCopiedFqn(
            self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_COMPANY
        );
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('found 3 errors validating Company');
        $this->getEntityFactory()->create(
            $companyFqn,
            new class($companyFqn) implements DataTransferObjectInterface
            {
                /**
                 * @var string
                 */
                private static $companyFqn;

                /**
                 * @var \Ramsey\Uuid\UuidInterface
                 */
                private static $uuid;

                /**
                 * @param string $companyFqn
                 */
                public function __construct(string $companyFqn)
                {
                    self::$companyFqn = $companyFqn;
                    self::$uuid       = Uuid::uuid4();
                }

                public static function getEntityFqn(): string
                {
                    return self::$companyFqn;
                }

                public function getId()
                {
                    return self::$uuid;
                }
            }
        );
    }

    /**
     * @test
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function canCreateValidEntityWithRequiredRelations()
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
        $companyDirectorDto->getCompanies()->add($companyDto);
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