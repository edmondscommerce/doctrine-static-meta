<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\E\CodeGeneration\Command;

use Doctrine\Common\Inflector\Inflector;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateFieldCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class GenerateFieldMultipleTimesTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Tests\Large\CodeGeneration\Command
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateFieldCommand
 */
class GenerateFieldMultipleTimesTest extends AbstractCommandTest
{

    public const WORK_DIR = AbstractTest::VAR_PATH .
                            '/' .
                            self::TEST_TYPE_LARGE .
                            '/GenerateSameFieldMultipleTime/';
    /**
     * @var array
     */
    private $entityName;
    /**
     * @var CommandTester
     */
    private $fieldGenerator;

    /**
     * @throws DoctrineStaticMetaException
     */
    public function setup(): void
    {
        parent::setUp();
        $this->entityName     = $this->getTestEntityFqns();
        $generateCommand      = $this->container->get(GenerateFieldCommand::class);
        $this->fieldGenerator = $this->getCommandTester($generateCommand);
    }

    /**
     * @test
     * @large
     *      */
    public function itShouldNotBePossibleToGenerateTheSameFieldTwice(): void
    {
        $type      = MappingHelper::TYPE_STRING;
        $fieldName = $this->getNameSpace('should_not_error');
        /* Generate the field */
        $this->generateField($fieldName, $type);
        /* And Again */
        $this->expectException(DoctrineStaticMetaException::class);
        $this->generateField($fieldName, $type);
    }

    /**
     * @param string $fieldName
     *
     * @return string
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private function getNameSpace(string $fieldName): string
    {
        $classy    = Inflector::classify($fieldName);
        $namespace = static::TEST_PROJECT_ROOT_NAMESPACE . AbstractGenerator::ENTITY_FIELD_TRAIT_NAMESPACE;

        return "$namespace\\$classy\\$classy";
    }

    private function generateField(string $fullyQualifiedName, string $type): void
    {
        $this->fieldGenerator->execute(
            [
                '-' . GenerateFieldCommand::OPT_PROJECT_ROOT_PATH_SHORT      => self::WORK_DIR,
                '-' . GenerateFieldCommand::OPT_PROJECT_ROOT_NAMESPACE_SHORT => self::TEST_PROJECT_ROOT_NAMESPACE,
                '--' . GenerateFieldCommand::OPT_FQN                         => $fullyQualifiedName,
                '--' . GenerateFieldCommand::OPT_TYPE                        => $type,
            ]
        );
    }
}
