<?php declare(strict_types=1);
/**
 * @category EdmondsCommerce
 * @package  EdmondsCommerce_
 * @author   Ross Mitchell <ross@edmondscommerce.co.uk>
 */

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use Doctrine\Common\Util\Inflector;
use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateFieldMultipleTimesTest extends AbstractCommandIntegrationTest
{

    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/'.self::TEST_TYPE.'/GenerateSameFieldMultipleTime/';
    /**
     * @var array
     */
    private $entityName;
    /**
     * @var CommandTester
     */
    private $fieldGenerator;

    /**
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public function setup(): void
    {
        parent::setup();
        $this->entityName     = $this->generateEntities();
        $generateCommand      = $this->container->get(GenerateFieldCommand::class);
        $this->fieldGenerator = $this->getCommandTester($generateCommand);
    }

    public function testItShouldNotBePossibleToGenerateTheSameFieldTwice(): void
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
