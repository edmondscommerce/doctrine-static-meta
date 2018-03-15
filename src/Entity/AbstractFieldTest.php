<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity;

use ReflectionClass;

abstract class AbstractFieldTest extends AbstractTest
{
    protected $testSubjectFqn;

    public function setup()
    {
        $this->createTestEntity();
        $this->loadTestEntity();
        $this->setTestField();
    }

    protected function createTestEntity()
    {

    }

    protected function loadTestEntity()
    {

    }

    protected function setTestField()
    {

    }

    // complete tests

    public function tearDown()
    {
        // Remove test entity
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    protected function getTestSubjectFqn(): string
    {
        if (! $this->testSubjectFqn) {
            $ref                  = new ReflectionClass($this);
            $namespace            = $ref->getNamespaceName();
            $shortName            = $ref->getShortName();
            $className            = substr($shortName, 0, strpos($shortName, 'Test'));
            $this->testSubjectFqn = $namespace.'\\'.$className;
        }

        return $this->testSubjectFqn;
    }
}