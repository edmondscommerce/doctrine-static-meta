<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use Doctrine\Common\Util\Inflector;
use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;

class GenerateFieldCommandTest extends AbstractCommandTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH.'/GenerateFieldCommandTest/';

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws DoctrineStaticMetaException
     */
    public function testGenerateEntity()
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
