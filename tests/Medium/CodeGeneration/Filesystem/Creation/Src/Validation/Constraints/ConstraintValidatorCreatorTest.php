<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\CodeGeneration\Filesystem\Creation\Src\Validation\Constraints;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Creation\Src\Validation\Constraints\ConstraintValidatorCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\Writer;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Small\ConfigTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Creation\Src\Validation\Constraints\ConstraintCreator
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Creation\AbstractCreator
 */
class ConstraintValidatorCreatorTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_MEDIUM . '/ConstraintValidatorCreatorTest';

    /**
     * @test
     * @medium
     */
    public function itCanCreateANewFileObjectWithTheCorrectContent()
    {
        $newObjectFqn = self::TEST_PROJECT_ROOT_NAMESPACE . '\\Validation\\Constraints\\IsBlueValidator';
        $template     = $this->getConstraintTemplate();
        $template->createTargetFileObject($newObjectFqn);
        $path = $template->write();
        self::assertFileExists($path);
        $expected = '<?php declare(strict_types=1);

namespace TemplateNamespace\Validation\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * This is a template constraint validator.
 *
 * The Constraint Validator is in charge of actually doing the validation.
 *
 * See the comment on the constructor about brining in dependencies if required to perform the validation
 *
 * @see https://symfony.com/doc/2.6/cookbook/validation/custom_constraint.html
 *
 * Suggest using the `./bin/doctrine dsm:override:create` command to create a new
 * override straight after you generate the file.
 *
 * Then you can edit it and update your override as required using
 * `./bin/doctrine dsm:overrides:update -a fromProject`
 *
 * And then reapply after a new build using
 * `./bin/doctrine dsm:overrides:update -a toProject`
 *
 */
class IsBlueValidator extends ConstraintValidator
{

    /**
     * If your validator requires dependencies to be injected, then simply declare them here.
     *
     * You also need to add this class to your list of Services in your DI container
     *
     * Finally you need to ensure that your container is configured to use the ContainerConstraintValidatorFactory
     *
     * @see \EdmondsCommerce\DoctrineStaticMeta\Container::setContainerBasedValidatorFactory
     */
    public function __construct()
    {
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed      $value      The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        // TODO: Implement validate() method.
    }
}';
        self::assertSame($expected, \ts\file_get_contents($path));
    }

    private function getConstraintTemplate(): ConstraintValidatorCreator
    {
        $namespaceHelper = new NamespaceHelper();

        $template =
            new ConstraintValidatorCreator(
                new FileFactory($namespaceHelper, new Config(ConfigTest::SERVER)),
                new FindReplaceFactory(),
                $namespaceHelper,
                new Writer()
            );
        $template->setProjectRootNamespace(self::TEST_PROJECT_ROOT_NAMESPACE)
                 ->setProjectRootDirectory(self::WORK_DIR);

        return $template;

    }
}