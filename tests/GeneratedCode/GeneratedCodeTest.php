<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\GeneratedCode;

use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\EntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;

class GeneratedCodeTest extends AbstractTest
{
    const WORK_DIR = __DIR__ . '/../../var/GeneratedCodeTest';

    const TEST_ENTITIES = [
        self::TEST_NAMESPACE . '\\Person',
        self::TEST_NAMESPACE . '\\Attributes\\Address',
        self::TEST_NAMESPACE . '\\Attributes\\Email',
        self::TEST_NAMESPACE . '\\Company',
        self::TEST_NAMESPACE . '\\Company\\Director'
    ];

    public function setup()
    {
        parent::setUp();
        $entityGenerator = new EntityGenerator(
            static::TEST_PROJECT_ROOT_NAMESPACE,
            static::WORK_DIR,
            static::TEST_PROJECT_ENTITIES_NAMESPACE
        );
        $relationsGenerator = new RelationsGenerator(
            static::TEST_PROJECT_ROOT_NAMESPACE,
            static::WORK_DIR,
            static::TEST_PROJECT_ENTITIES_NAMESPACE
        );
        foreach (static::TEST_ENTITIES as $fqn) {
            $entityGenerator->generateEntity($fqn);
            $relationsGenerator->generateRelationTraitsForEntity($fqn);
        }
    }

    public function testRunTests()
    {
        $docRoot=__DIR__.'/../../';
        exec($docRoot.'bin/phpunit ' . $docRoot . 'var/GeneratedCodeTest/tests');
    }
}
