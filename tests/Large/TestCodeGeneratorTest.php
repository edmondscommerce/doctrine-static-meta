<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;

/**
 * @coversNothing
 * @large
 */
class TestCodeGeneratorTest extends AbstractTest
{
    public const WORK_DIR = self::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/TestCodeGeneratorTest';

    protected static $buildOnce = true;

    public function setup()
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getTestCodeGenerator()
                 ->copyTo(self::WORK_DIR);
            self::$built = true;
        }
    }

    /**
     * We need to ensure that the test code that is used everywhere is actually valid
     *
     * That's what this test is for
     *
     * @test
     */
    public function testCodeIsValid()
    {
        $this->qaGeneratedCode();
    }

    /**
     * @test
     */
    public function canCreateEmail()
    {
        $emailFqn = $this->getEntityFqn(TestCodeGenerator::TEST_ENTITY_EMAIL);
        $email    = $this->getEntityFactory()->create($emailFqn);
        self::assertInstanceOf($emailFqn, $email);

        return $email;
    }

    private function getEntityFqn(string $testEntitySubFqn): string
    {
        return self::TEST_ENTITIES_ROOT_NAMESPACE . $testEntitySubFqn;
    }

    /**
     * @test
     * @depends canCreateEmail
     *
     * @param EntityInterface $email
     */
    public function canCreatePerson(EntityInterface $email): void
    {
        $personFqn = $this->getEntityFqn(TestCodeGenerator::TEST_ENTITY_PERSON);
        $person    = $this->getEntityFactory()->create(
            $personFqn,
            new class($email) implements DataTransferObjectInterface
            {
                private $email;

                public function __construct($email)
                {
                    $this->email = $email;
                }

                public function getEmail()
                {
                    return $this->email;
                }
            }
        );
        self::assertInstanceOf($personFqn, $person);
    }

    /**
     * @test
     */
    public function canCreateAttributesAddress(): void
    {
        $addressFqn = $this->getEntityFqn(TestCodeGenerator::TEST_ENTITY_ATTRIBUTES_ADDRESS);
        $address    = $this->getEntityFactory()->create($addressFqn);
        self::assertInstanceOf($addressFqn, $address);
    }

    /**
     * @test
     */
    public function canCreateCompany(): void
    {
        $companyFqn = $this->getEntityFqn(TestCodeGenerator::TEST_ENTITY_COMPANY);
        $company    = $this->getEntityFactory()->create($companyFqn);
        self::assertInstanceOf($companyFqn, $company);
    }
}
