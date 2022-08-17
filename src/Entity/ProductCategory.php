<?php

namespace App\Entity;

use App\Repository\ProductCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductCategoryRepository::class)]
class ProductCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return \App\Entity\Product[]|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getProducts(): ArrayCollection|array
    {
        return $this->products;
    }

    /**
     * @param \App\Entity\Product[]|\Doctrine\Common\Collections\ArrayCollection $products
     */
    public function setProducts(ArrayCollection|array $products): void
    {
        $this->products = $products;
    }
}
