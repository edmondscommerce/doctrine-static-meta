<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Creation\Src\Validation\Constraints\ConstraintCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Creation\Src\Validation\Constraints\ConstraintValidatorCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;

class CreateConstraintAction
{
    private const SUFFIX_CONSTRAINT           = 'Constraint';
    private const SUFFIX_CONSTRAINT_VALIDATOR = 'ConstraintValidator';

    /**
     * @var ConstraintCreator
     */
    protected $constraintCreator;
    /**
     * @var ConstraintValidatorCreator
     */
    protected $constraintValidatorCreator;

    private $constraintsRootNamespace;

    public function __construct(
        ConstraintCreator $constraintCreator,
        ConstraintValidatorCreator $constraintValidatorCreator,
        NamespaceHelper $namespaceHelper,
        Config $config
    ) {
        $this->constraintCreator          = $constraintCreator;
        $this->constraintValidatorCreator = $constraintValidatorCreator;
        $this->setProjectRootNamespace($namespaceHelper->getProjectRootNamespaceFromComposerJson());
        $this->setProjectRootDirectory($config::getProjectRootDirectory());
    }

    public function setProjectRootNamespace(string $projectRootNamespace): self
    {
        $this->constraintCreator->setProjectRootNamespace($projectRootNamespace);
        $this->constraintValidatorCreator->setProjectRootNamespace($projectRootNamespace);
        $this->constraintsRootNamespace = $projectRootNamespace . '\\Validation\\Constraints';

        return $this;
    }

    public function setProjectRootDirectory(string $projectRootDirectory): self
    {
        $this->constraintCreator->setProjectRootDirectory($projectRootDirectory);
        $this->constraintValidatorCreator->setProjectRootDirectory($projectRootDirectory);

        return $this;
    }

    public function run(string $constraintShortName)
    {
        $this->createConstraint($constraintShortName);
        $this->createConstraintValidator($constraintShortName);

    }

    private function createConstraint(string $constraintShortName): string
    {
        $constraintFqn = $this->constraintsRootNamespace . '\\' . $constraintShortName . self::SUFFIX_CONSTRAINT;
        $this->constraintCreator->createTargetFileObject($constraintFqn)->write();
    }

    private function createConstraintValidator(string $constraintShortName): string
    {
        $constraintValidatorFqn = $this->constraintsRootNamespace . '\\' . $this->stripSuffix($constraintShortName) .
                                  self::SUFFIX_CONSTRAINT_VALIDATOR;
        $this->constraintCreator->createTargetFileObject($constraintValidatorFqn)->write();
    }

    private function stripSuffix(string $constraintShortName): string
    {
        $pos = \ts\strpos($constraintShortName, self::SUFFIX_CONSTRAINT);
        if (false === $pos) {
            return $constraintShortName;
        }

        return substr($constraintShortName, 0, $pos);

    }
}