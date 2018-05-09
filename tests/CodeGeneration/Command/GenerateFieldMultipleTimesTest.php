<?php
/**
 * @category EdmondsCommerce
 * @package  EdmondsCommerce_
 * @author   Ross Mitchell <ross@edmondscommerce.co.uk>
 */

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use Doctrine\Common\Util\Inflector;
use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateFieldMultipleTimesTest extends AbstractCommandTest
{

    public const WORK_DIR = AbstractTest::VAR_PATH.'/GenerateSameFieldMultipleTime/';
    /**
     * @var array
     */
    private $entityName;
    /**
     * @var CommandTester
     */
    private $commandTester;

    private $fieldGenerator;

    /**
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public function setup()
    {
        parent::setup();
        $this->entityName     = $this->generateEntities();
        $generateCommand      = $this->container->get(GenerateFieldCommand::class);
        $this->fieldGenerator = $this->getCommandTester($generateCommand);
        $command              = $this->container->get(SetFieldCommand::class);
        $this->commandTester  = $this->getCommandTester($command);
    }


    public function testGeneratingAFieldMultipleTimesWillProduceValidCode()
    {
        $type           = MappingHelper::TYPE_STRING;
        $fieldName      = $this->getNameSpace('should_not_error');
        $differentField = $this->getNameSpace('different_field');
        /* Generate the field */
        $this->generateField($fieldName, $type);
        /* And Again */
        $this->generateField($fieldName, $type);
        /* Once More For Good Luck */
        $this->generateField($fieldName, $type);
        /* We need to actually load the entity to check if it is valid so add another field */
        $this->generateField($differentField, $type);
        /* Now set the relationship */
        $entityName = array_pop($this->entityName);
        $this->setField($fieldName, $entityName);
        $secondsEntityName = array_pop($this->entityName);
        $this->setField($fieldName, $secondsEntityName);
        $this->setField($differentField, $entityName);
        $this->qaGeneratedCode();

    }

    private function getNameSpace($fieldName)
    {
        $classy    = Inflector::classify($fieldName);
        $namespace = static::TEST_PROJECT_ROOT_NAMESPACE.AbstractGenerator::ENTITY_FIELD_TRAIT_NAMESPACE;

        return "$namespace\\$classy\\$classy";
    }

    private function generateField($fullyQualifiedName, $type)
    {
        return $this->fieldGenerator->execute(
            [
                '-'.GenerateFieldCommand::OPT_PROJECT_ROOT_PATH_SHORT      => self::WORK_DIR,
                '-'.GenerateFieldCommand::OPT_PROJECT_ROOT_NAMESPACE_SHORT => self::TEST_PROJECT_ROOT_NAMESPACE,
                '--'.GenerateFieldCommand::OPT_FQN                         => $fullyQualifiedName,
                '--'.GenerateFieldCommand::OPT_TYPE                        => $type,
            ]
        );
    }

    private function setField($fieldName, $entityName)
    {
        $this->commandTester->execute(
            [
                '-'.SetFieldCommand::OPT_FIELD_SHORT  => $fieldName.'FieldTrait',
                '-'.SetFieldCommand::OPT_ENTITY_SHORT => $entityName,
            ]
        );
    }
}
