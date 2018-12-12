<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\Entity\Validation;

use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

/**
 * @medium
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\Initialiser
 */
class InitialiserTest extends AbstractTest
{
    public function setUp()
    {
        parent::setUp();
        $this->generateTestCode();
    }

    /**
     * @test
     */
    public function itInitialisesAProxy(): void
    {

    }

    /**
     * @test
     */
    public function itInitalisesAnEntityThatHasUninitialisedCollections(): void
    {

    }
}