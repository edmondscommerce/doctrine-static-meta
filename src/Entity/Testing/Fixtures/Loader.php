<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures;

use RuntimeException;

class Loader extends \Doctrine\Common\DataFixtures\Loader
{
    /**
     * @var FixturesHelper
     */
    private $fixturesHelper;

    /**
     * @param FixturesHelper $fixturesHelper
     *
     * @return Loader
     */
    public function setFixturesHelper(FixturesHelper $fixturesHelper): Loader
    {
        $this->fixturesHelper = $fixturesHelper;

        return $this;
    }

    protected function createFixture($class)
    {
        if (false === ($this->fixturesHelper instanceof FixturesHelper)) {
            throw new RuntimeException('Please call setFixturesHelper before using this method');
        }

        return $this->fixturesHelper->createFixture($class);
    }
}
