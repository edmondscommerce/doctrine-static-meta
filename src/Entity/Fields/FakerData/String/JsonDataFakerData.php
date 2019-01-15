<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\AbstractFakerDataProvider;

class JsonDataFakerData extends AbstractFakerDataProvider
{
    public function __invoke()
    {
        $dataArray = [
            'email' => $this->generator->email,
            'name'  => $this->generator->name,
        ];

        return json_encode($dataArray);
    }
}
