<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: ProductCategory::class)]
    #[ORM\JoinColumn(name: 'product_category_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?ProductCategory $productCategory = null;

    /**
     * @var \App\Entity\ProductImage[]|\Doctrine\Common\Collections\ArrayCollection
     */
    #[ORM\OneToMany(mappedBy: 'product_id', targetEntity: ProductImage::class, cascade: ['persist'], fetch: 'EXTRA_LAZY')]
    private array $images;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return \App\Entity\ProductImage[]|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getImages(): ArrayCollection|array
    {
        return $this->images;
    }

    /**
     * @param \App\Entity\ProductImage[]|\Doctrine\Common\Collections\ArrayCollection $images
     */
    public function setImages(ArrayCollection|array $images): void
    {
        $this->images = $images;
    }

    /**
     * @return \App\Entity\ProductCategory|null
     */
    public function getProductCategory(): ?ProductCategory
    {
        return $this->productCategory;
    }

    /**
     * @param \App\Entity\ProductCategory|null $productCategory
     */
    public function setProductCategory(?ProductCategory $productCategory): void
    {
        $this->productCategory = $productCategory;
    }
}
