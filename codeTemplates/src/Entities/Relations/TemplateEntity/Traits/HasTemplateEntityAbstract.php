<?php declare(strict_types=1);

namespace TemplateNamespace\Entities\Relations\TemplateEntity\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaData;
use TemplateNamespace\Entities\TemplateEntity;

trait HasTemplateEntityAbstract
{
    use ReciprocatesTemplateEntity;

    /**
     * @var TemplateEntity|null
     */
    private $templateEntity = null;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract public static function getPropertyMetaForTemplateEntity(ClassMetadataBuilder $builder);

    /**
     * @return TemplateEntity|null
     */
    public function getTemplateEntity(): ?TemplateEntity
    {
        return $this->templateEntity;
    }

    /**
     * @param TemplateEntity $templateEntity
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaData
     */
    public function setTemplateEntity(TemplateEntity $templateEntity, bool $recip = true): UsesPHPMetaData
    {
        if (true === $recip) {
            $this->reciprocateRelationOnTemplateEntity($templateEntity);
        }
        $this->templateEntity = $templateEntity;

        return $this;
    }

    /**
     * @return $this|UsesPHPMetaData
     */
    public function removeTemplateEntity(): UsesPHPMetaData
    {
        $this->templateEntity = null;

        return $this;
    }
}
