<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\Entity\Traits;

use EdmondsCommerce\DoctrineStaticMeta\Exception\ValidationException;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;

/**
 * @medium
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Traits\AlwaysValidTrait
 */
class AlwaysValidTraitTest extends AbstractTest
{
    public const WORK_DIR = self::VAR_PATH . '/' . self::TEST_TYPE_MEDIUM . '/AlwaysValidTraitTest';

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
        $this->getEntityFactory()->create($companyFqn);
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

        $companyDirectorFqn = $this->getCopiedFqn(
            self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_DIRECTOR
        );
        $companyDirector    = $this->getEntityFactory()->create($companyDirectorFqn);
        $companyDto->getCompanyDirectors()->add($companyDirector);

        $this->getEntityFactory()->create(
            $companyFqn,
            $companyDto
        );


    }
}