<?php

namespace App\Model\Product;

use App\Entity\Product;

class ProductDataFactory
{
    public function create(): ProductData
    {
        return new ProductData();
    }

    public function createFromProduct(Product $product): ProductData
    {
        $data = $this->create();

        $data->name = $product->getName();
        $data->description = $product->getDescription();

        return $data;
    }
}
