<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Validation\Constraints\ConstraintCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Validation\Constraints\ConstraintValidatorCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;

class CreateConstraintAction implements ActionInterface
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

    /**
     * @var string
     */
    private $constraintsRootNamespace;

    /**
     * @var string
     */
    private $constraintShortName;

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

    /**
     * @param string $constraintShortName
     *
     * @return CreateConstraintAction
     */
    public function setConstraintShortName(string $constraintShortName): self
    {
        $this->constraintShortName = $constraintShortName;

        return $this;
    }

    public function run(): void
    {
        if (null === $this->constraintShortName) {
            throw new \RuntimeException('You must call setContraintShortname before calling run');
        }
        $this->createConstraint($this->constraintShortName);
        $this->createConstraintValidator($this->constraintShortName);
    }

    private function createConstraint(string $constraintShortName): void
    {
        $constraintFqn = $this->constraintsRootNamespace . '\\' . $this->stripSuffix($constraintShortName)
                         . self::SUFFIX_CONSTRAINT;
        $this->constraintCreator->createTargetFileObject($constraintFqn)->write();
    }

    private function stripSuffix(string $constraintShortName): string
    {
        if (false === \ts\stringContains($constraintShortName, self::SUFFIX_CONSTRAINT)) {
            return $constraintShortName;
        }

        $pos = \ts\strpos($constraintShortName, self::SUFFIX_CONSTRAINT);

        return substr($constraintShortName, 0, $pos);
    }

    private function createConstraintValidator(string $constraintShortName): void
    {
        $constraintValidatorFqn = $this->constraintsRootNamespace . '\\' . $this->stripSuffix($constraintShortName) .
                                  self::SUFFIX_CONSTRAINT_VALIDATOR;
        $this->constraintCreator->createTargetFileObject($constraintValidatorFqn)->write();
    }
}
