<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Validation\Constraints\EntityIsValidConstraintCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Validation\Constraints\EntityIsValidConstraintValidatorCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Validation\Constraints\PropertyConstraintCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Validation\Constraints\PropertyConstraintValidatorCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;

class CreateConstraintAction implements ActionInterface
{
    private const SUFFIX_CONSTRAINT           = 'Constraint';
    private const SUFFIX_CONSTRAINT_VALIDATOR = 'ConstraintValidator';

    public const OPTION_PROPERTY = 'property';
    public const OPTION_ENTITY   = 'entity';

    /**
     * @var PropertyConstraintCreator
     */
    protected $propertyConstraintCreator;
    /**
     * @var PropertyConstraintValidatorCreator
     */
    protected $propertyConstraintValidatorCreator;

    /**
     * @var string
     */
    private $constraintsRootNamespace;

    /**
     * @var string
     */
    private $constraintShortName;
    /**
     * @var string
     */
    private $propertyOrEntity = self::OPTION_PROPERTY;
    /**
     * @var EntityIsValidConstraintCreator
     */
    private $entityIsValidConstraintCreator;
    /**
     * @var EntityIsValidConstraintValidatorCreator
     */
    private $entityIsValidConstraintValidatorCreator;

    public function __construct(
        PropertyConstraintCreator $constraintCreator,
        PropertyConstraintValidatorCreator $constraintValidatorCreator,
        EntityIsValidConstraintCreator $entityIsValidConstraintCreator,
        EntityIsValidConstraintValidatorCreator $entityIsValidConstraintValidatorCreator,
        NamespaceHelper $namespaceHelper,
        Config $config
    ) {
        $this->propertyConstraintCreator               = $constraintCreator;
        $this->propertyConstraintValidatorCreator      = $constraintValidatorCreator;
        $this->entityIsValidConstraintCreator          = $entityIsValidConstraintCreator;
        $this->entityIsValidConstraintValidatorCreator = $entityIsValidConstraintValidatorCreator;
        $this->setProjectRootNamespace($namespaceHelper->getProjectRootNamespaceFromComposerJson());
        $this->setProjectRootDirectory($config::getProjectRootDirectory());
    }

    public function setProjectRootNamespace(string $projectRootNamespace): self
    {
        $this->propertyConstraintCreator->setProjectRootNamespace($projectRootNamespace);
        $this->propertyConstraintValidatorCreator->setProjectRootNamespace($projectRootNamespace);
        $this->constraintsRootNamespace = $projectRootNamespace . '\\Validation\\Constraints';

        return $this;
    }

    public function setProjectRootDirectory(string $projectRootDirectory): self
    {
        $this->propertyConstraintCreator->setProjectRootDirectory($projectRootDirectory);
        $this->propertyConstraintValidatorCreator->setProjectRootDirectory($projectRootDirectory);

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

    public function setPropertyOrEntity(string $propertyOrEntity): self
    {
        if (false === \in_array($propertyOrEntity, [self::OPTION_PROPERTY, self::OPTION_ENTITY], true)) {
            throw new \InvalidArgumentException(
                '$propertyOrEntity must be one of self::OPTION_PROPERTY,self::OPTION_ENTITY'
            );
        }
        $this->propertyOrEntity = $propertyOrEntity;

        return $this;
    }

    public function run(): void
    {
        if (null === $this->constraintShortName) {
            throw new \RuntimeException('You must call setContraintShortname before calling run');
        }
        if (self::OPTION_PROPERTY === $this->propertyOrEntity) {
            $this->createPropertyConstraint($this->constraintShortName);
            $this->createPropertyConstraintValidator($this->constraintShortName);

            return;
        }
        if (self::OPTION_ENTITY === $this->propertyOrEntity) {
            $this->createEntityConstraint($this->constraintShortName);
            $this->createEntityConstraintValidator($this->constraintShortName);

            return;
        }

        throw new \LogicException('Invalid propertyOrEntity ' . $this->propertyOrEntity);
    }

    private function createPropertyConstraint(string $constraintShortName): void
    {
        $constraintFqn = $this->constraintsRootNamespace . '\\' . $this->stripSuffix($constraintShortName)
                         . self::SUFFIX_CONSTRAINT;
        $this->propertyConstraintCreator->createTargetFileObject($constraintFqn)->write();
    }

    private function stripSuffix(string $constraintShortName): string
    {
        if (false === \ts\stringContains($constraintShortName, self::SUFFIX_CONSTRAINT)) {
            return $constraintShortName;
        }

        $pos = \ts\strpos($constraintShortName, self::SUFFIX_CONSTRAINT);

        return substr($constraintShortName, 0, $pos);
    }

    private function createPropertyConstraintValidator(string $constraintShortName): void
    {
        $constraintValidatorFqn = $this->constraintsRootNamespace . '\\' . $this->stripSuffix($constraintShortName) .
                                  self::SUFFIX_CONSTRAINT_VALIDATOR;
        $this->propertyConstraintValidatorCreator->createTargetFileObject($constraintValidatorFqn)->write();
    }

    private function createEntityConstraint(string $constraintShortName): void
    {
        $constraintFqn = $this->constraintsRootNamespace . '\\' . $this->stripSuffix($constraintShortName)
                         . self::SUFFIX_CONSTRAINT;
        $this->entityIsValidConstraintCreator->createTargetFileObject($constraintFqn)->write();
    }

    private function createEntityConstraintValidator(string $constraintShortName): void
    {
        $constraintValidatorFqn = $this->constraintsRootNamespace . '\\' . $this->stripSuffix($constraintShortName) .
                                  self::SUFFIX_CONSTRAINT_VALIDATOR;
        $this->entityIsValidConstraintValidatorCreator->createTargetFileObject($constraintValidatorFqn)->write();
    }
}
