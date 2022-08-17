<?php

namespace App\Model\Product\Api;

use App\Exception\ProductNotFoundException;
use App\Model\Product\ProductCacheFacade;
use App\Model\Product\ProductFacade;
use App\Repository\ProductElasticSearchRepository;
use Psr\Cache\InvalidArgumentException;

class ProductApiFilterFacade
{
    private ProductFacade $productFacade;
    private ProductCacheFacade $productCacheFacade;
    private ProductElasticSearchRepository $productElasticSearchRepository;

    public function __construct(ProductFacade                  $productFacade,
                                ProductCacheFacade             $productCacheFacade,
                                ProductElasticSearchRepository $productElasticSearchRepository
    )
    {
        $this->productFacade = $productFacade;
        $this->productCacheFacade = $productCacheFacade;
        $this->productElasticSearchRepository = $productElasticSearchRepository;
    }

    public function getById(int $id): array
    {
        try {
            return $this->productCacheFacade->get($id, fn($id) => $this->productFacade->getById($id));
        } catch (InvalidArgumentException | ProductNotFoundException $e) {
            throw new ProductNotFoundException('');
        }
    }

    /**
     * @param int[] $ids
     * @return array<array>
     */
    public function findAllByIds(array $ids): array
    {
        $products = array_map(function (int $id) {
            try {
                $this->productCacheFacade->get($id, fn($id) => $this->productFacade->findById($id));
            } catch (InvalidArgumentException) {
                return null;
            }
        }, $ids);

        return array_filter($products);
    }

    /**
     * @param string $text
     * @return \App\Entity\Product[]
     */
    public function findAllByText(string $text): array
    {
        return $this->findAllByIds(
            $this->productElasticSearchRepository->getIdsBySearchText($text)
        );
    }

    /**
     * @param int $page
     * @return \App\Entity\Product[]
     */
    public function findAll(int $page): array
    {
        return $this->findAllByIds(
            $this->productElasticSearchRepository->getIdsByPage($page)
        );
    }
}
