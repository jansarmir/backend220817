<?php

namespace App\Model\Product\Api;

use App\Entity\Product;

class ProductApiMapper
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
