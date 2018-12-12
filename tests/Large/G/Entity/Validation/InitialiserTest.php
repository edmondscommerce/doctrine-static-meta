<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Validation;

use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractLargeTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;

/**
 * @medium
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\Initialiser
 */
class InitialiserTest extends AbstractLargeTest
{
    public const WORK_DIR = self::VAR_PATH . '/' . self::TEST_TYPE_MEDIUM . '/InitialiserTest';

    private const TEST_ENTITY_FQN = self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_PERSON;

    private $testEntityFqn;

    public function setUp()
    {
        parent::setUp();
        $this->generateTestCode();
        $this->setupCopiedWorkDir();
        $this->testEntityFqn = $this->getCopiedFqn(self::TEST_ENTITY_FQN);
        $this->setupDbWithFixtures();
    }

    private function setupDbWithFixtures(): void
    {
        $fixturesHelper = $this->getFixturesHelper();
        $fixturesHelper->createDb(
            $fixturesHelper->createFixture(
                $this->getNamespaceHelper()->getFixtureFqnFromEntityFqn($this->testEntityFqn)
            )
        );
    }

    /**
     * @test
     */
    public function itInitialisesAProxy(): void
    {
        $expected = false;
        $actual   = true;
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function itInitalisesAnEntityThatHasUninitialisedCollections(): void
    {
        $expected = false;
        $actual   = true;
        self::assertSame($expected, $actual);
    }
}