<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use Doctrine\Common\Util\Inflector;
use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;

class GenerateFieldCommandTest extends AbstractCommandIntegrationTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/'.self::TEST_TYPE.'/GenerateFieldCommandTest/';

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function testGenerateField()
    {
        $command    = $this->container->get(GenerateFieldCommand::class);
        $tester     = $this->getCommandTester($command);
        $fieldsPath = self::WORK_DIR.'src/Entity/Fields';
        $namespace  = self::TEST_PROJECT_ROOT_NAMESPACE . AbstractGenerator::ENTITY_FIELD_TRAIT_NAMESPACE;

        $createdFiles = [];

        foreach (MappingHelper::COMMON_TYPES as $type) {
            $classy   = Inflector::classify($type);
            $fieldFqn = $namespace . "\\$classy\\$classy";
            $tester->execute(
                [
                    '-'.GenerateFieldCommand::OPT_PROJECT_ROOT_PATH_SHORT      => self::WORK_DIR,
                    '-'.GenerateFieldCommand::OPT_PROJECT_ROOT_NAMESPACE_SHORT => self::TEST_PROJECT_ROOT_NAMESPACE,
                    '-'.GenerateFieldCommand::OPT_FQN_SHORT                    => $fieldFqn,
                    '-'.GenerateFieldCommand::OPT_TYPE_SHORT                   => $type
                ]
            );

            $createdFiles[] = "$fieldsPath/Interfaces/$classy/{$classy}FieldInterface.php";
            $createdFiles[] = "$fieldsPath/Traits/$classy/{$classy}FieldTrait.php";
        }

        foreach ($createdFiles as $createdFile) {
            $this->assertNoMissedReplacements($createdFile);
        }

        $this->qaGeneratedCode();
    }
}