<?php

namespace App\Model\Product;

use App\Entity\Product;
use App\Exception\ProductNotFoundException;
use App\Model\Product\ElasticSearch\ProductElasticSearchMapper;
use App\Repository\ProductElasticSearchRepository;
use App\Repository\ProductRepository;

class ProductFacade
{
    private ProductRepository $productRepository;
    private ProductElasticSearchRepository $productElasticSearchRepository;
    private ProductElasticSearchMapper $productElasticSearchMapper;
    private ProductCacheFacade $productCacheFacade;

    public function __construct(ProductRepository              $productRepository,
                                ProductElasticSearchRepository $productElasticSearchRepository,
                                ProductElasticSearchMapper     $productElasticSearchMapper,
                                ProductCacheFacade $productCacheFacade
    )
    {
        $this->productRepository = $productRepository;
        $this->productElasticSearchRepository = $productElasticSearchRepository;
        $this->productElasticSearchMapper = $productElasticSearchMapper;
        $this->productCacheFacade = $productCacheFacade;
    }

    public function findById(int $id): ?Product
    {
        return $this->productRepository->find($id);
    }

    /**
     * @throws \App\Exception\ProductNotFoundException
     */
    public function getById(int $id): Product
    {
        $entity = $this->productRepository->find($id);

        if ($entity === null) {
            throw new ProductNotFoundException('Product not found');
        }

        return $entity;
    }

    public function delete(Product $product): void
    {
        $this->productRepository->remove($product, true);
        $this->productCacheFacade->delete($product->getId());
    }

    /**
     * @throws \App\Exception\ProductNotFoundException
     */
    public function deleteById(int $id): void
    {
        $this->delete($this->getById($id));
    }

    public function update(Product $product): void
    {
        $this->productRepository->flush($product);
        $this->productCacheFacade->create($product);
        $this->productElasticSearchRepository->exportProductArray(
            $this->productElasticSearchMapper->createArrayFromProduct($product)
        );
    }

    public function updateFromData(Product $product, ProductData $data): void
    {
        $product->setName($data->name);
        $product->setDescription($data->description);
        //$product->setProductCategory($data->productCategory);
        //$product->setImages([]);

        $this->update($product);
    }

    public function createFromData(ProductData $data): Product
    {
        $product = new Product();

        $this->updateFromData($product, $data);

        $this->productRepository->add($product, true);
        $this->productCacheFacade->create($product);
        $this->productElasticSearchRepository->exportProductArray(
            $this->productElasticSearchMapper->createArrayFromProduct($product)
        );

        return $product;
    }
}
