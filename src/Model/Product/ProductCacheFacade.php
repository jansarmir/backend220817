<?php

namespace App\Model\Product;

use App\Entity\Product;
use App\Model\Product\Api\ProductApiMapper;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class ProductCacheFacade
{
    private CacheInterface $cache;
    private ProductApiMapper $productApiMapper;

    public function __construct(CacheInterface   $cache,
                                ProductApiMapper $productApiMapper)
    {
        $this->cache = $cache;
        $this->productApiMapper = $productApiMapper;
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function get(int $id, callable $callbackGetProduct): array
    {
        return $this->cache->get($this->getCacheKey($id), function (ItemInterface $item) use ($callbackGetProduct) {
            $item->expiresAfter(36000);
            $item->tag(['entity_product']);

            $product = $callbackGetProduct();

            if ($product === null) {
                return null;
            }

            return $this->productApiMapper->createArrayFromProduct($product);
        });
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function create(Product $product): void
    {
        $this->delete($product->getId());
        $this->get($product->getId(), fn() => $product);
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function delete(int $id): void
    {
        $this->cache->delete($this->getCacheKey($id));
    }

    private function getCacheKey(int $id): string
    {
        return 'entity_product_' . $id;
    }
}
