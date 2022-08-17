<?php

namespace App\Model\Product\ElasticSearch;

use App\Entity\Product;

class ProductElasticSearchMapper
{
    public function createArrayFromProduct(Product $product): array
    {
        return [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
        ];
    }
}
