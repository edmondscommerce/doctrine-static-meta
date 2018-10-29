<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey\IdFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey\IntegerIdFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey\NonBinaryUuidFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey\UuidFieldTrait;

class ReplaceEntityIdFieldProcess implements ProcessInterface
{

    public const FIND_USE_STATEMENT = 'use DSM\Fields\Traits\PrimaryKey\IdFieldTrait;';
    /**
     * @var string
     */
    private $idTraitFqn;

    /**
     * Specify the IdFieldTrait fully qualified name, eg UuidFieldTrait::class
     *
     * @param string $idTraitFqn
     *
     * @return ReplaceEntityIdFieldProcess
     */
    public function setIdTraitFqn(string $idTraitFqn): self
    {
        $this->idTraitFqn = $idTraitFqn;

        return $this;
    }

    public function run(File\FindReplace $findReplace): void
    {
        if (null === $this->idTraitFqn) {
            throw new \RuntimeException('you must set the IdTraitFqn');
        }
        $findReplace->findReplace(self::FIND_USE_STATEMENT, $this->getUseStatement());
    }

    /**
     * Get the use statement to replace it with
     *
     * @return string
     */
    private function getUseStatement(): string
    {
        switch ($this->idTraitFqn) {
            case IdFieldTrait::class:
                $useStatement = 'use DSM\Fields\Traits\PrimaryKey\IdFieldTrait;';
                break;
            case NonBinaryUuidFieldTrait::class:
                $useStatement = 'use DSM\Fields\Traits\PrimaryKey\NonBinaryUuidFieldTrait;';
                break;
            case UuidFieldTrait::class:
                $useStatement = 'use DSM\Fields\Traits\PrimaryKey\UuidFieldTrait;';
                break;
            default:
                throw new \LogicException('Unknown trait selected ' . $this->idTraitFqn);
        }

        return $useStatement;
    }
}
