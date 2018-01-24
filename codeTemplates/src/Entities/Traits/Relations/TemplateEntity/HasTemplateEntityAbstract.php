<?php declare(strict_types=1);

namespace TemplateNamespace\Entities\Traits\Relations\TemplateEntity;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
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
    abstract protected static function getAssociationMetaForTemplateEntity(ClassMetadataBuilder $builder);

    protected function getPropertyMetaForTemplateEntity(ClassMetadataBuilder $builder)
    {
        $builder->createField('id', Type::INTEGER)
            ->makePrimaryKey()
            ->nullable(false)
            ->generatedValue('IDENTITY')
            ->build();
    }

    /**
     * @return TemplateEntity|null
     */
    public function getTemplateEntity(): TemplateEntity
    {
        return $this->templateEntity;
    }

    /**
     * @param TemplateEntity $templateEntity
     * @param bool $recip
     *
     * @return $this
     */
    public function setTemplateEntity(TemplateEntity $templateEntity, bool $recip = true)
    {
        if (true === $recip) {
            $this->reciprocateRelationOnTemplateEntity($templateEntity);
        }
        $this->templateEntity = $templateEntity;

        return $this;
    }

    /**
     * @return $this
     */
    public function removeTemplateEntity()
    {
        $this->templateEntity = null;

        return $this;
    }
}
