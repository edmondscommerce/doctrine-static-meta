<?php declare(strict_types=1);
/**
 * @category EdmondsCommerce
 * @package  EdmondsCommerce_
 * @author   Ross Mitchell <ross@edmondscommerce.co.uk>
 */

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FindAndReplaceHelper;

class IdTrait
{
    public const ID_FIELD_TRAIT         = 1;
    public const INTEGER_ID_FIELD_TRAIT = 2;
    public const NON_BINARY_UUID_TRAIT  = 4;
    public const UUID_FIELD_TRAIT       = 8;
    /**
     * @var FindAndReplaceHelper
     */
    protected $findAndReplaceHelper;

    private $idTraitToUse = self::UUID_FIELD_TRAIT;

    public function __construct(FindAndReplaceHelper $findAndReplaceHelper)
    {
        $this->findAndReplaceHelper = $findAndReplaceHelper;
    }

    public function setIdTrait(int $type): void
    {
        switch ($type) {
            case self::ID_FIELD_TRAIT:
            case self::INTEGER_ID_FIELD_TRAIT:
            case self::NON_BINARY_UUID_TRAIT:
            case self::UUID_FIELD_TRAIT:
                $this->idTraitToUse = $type;
                break;
            default:
                throw new \LogicException("Unknown ID trait of $type given");
        }
    }

    public function updateEntity(string $filePath): void
    {
        if ($this->idTraitToUse === self::ID_FIELD_TRAIT) {
            return;
        }
        $useStatement = $this->getUseStatement();
        $this->findAndReplaceHelper->findReplace(
            'use DSM\Fields\Traits\PrimaryKey\IdFieldTrait;',
            $useStatement,
            $filePath
        );
    }

    private function getUseStatement()
    {
        switch ($this->idTraitToUse) {
            case self::ID_FIELD_TRAIT:
                $useStatement = 'use DSM\Fields\Traits\PrimaryKey\IdFieldTrait;';
                break;
            case self::INTEGER_ID_FIELD_TRAIT:
                $useStatement = 'use DSM\Fields\Traits\PrimaryKey\IntegerIdFieldTrait;';
                break;
            case self::NON_BINARY_UUID_TRAIT:
                $useStatement = 'use DSM\Fields\Traits\PrimaryKey\NonBinaryUuidFieldTrait;';
                break;
            case self::UUID_FIELD_TRAIT:
                $useStatement = 'use DSM\Fields\Traits\PrimaryKey\UuidFieldTrait;';
                break;
            default:
                throw new \LogicException('Unknown trait selected');
        }

        return $useStatement;
    }
}
