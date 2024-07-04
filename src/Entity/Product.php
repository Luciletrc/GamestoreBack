<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $uuid = null;

    #[ORM\OneToMany(mappedBy: 'product_id', targetEntity: Images::class, orphanRemoval: true)]
    private Collection $image_id;

    #[ORM\OneToOne(inversedBy: 'category_id', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category_id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $pegi = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\OneToMany(mappedBy: 'product_id', targetEntity: Stock::class, orphanRemoval: true)]
    private Collection $product_id;

    #[ORM\ManyToOne(inversedBy: 'product_id')]
    private ?Order $order_id = null;

    public function __construct()
    {
        $this->image_id = new ArrayCollection();
        $this->product_id = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    public function setUuid(Uuid $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @return Collection<int, Images>
     */
    public function getImageId(): Collection
    {
        return $this->image_id;
    }

    public function addImageId(Images $imageId): static
    {
        if (!$this->image_id->contains($imageId)) {
            $this->image_id->add($imageId);
            $imageId->setProductId($this);
        }

        return $this;
    }

    public function removeImageId(Images $imageId): static
    {
        if ($this->image_id->removeElement($imageId)) {
            // set the owning side to null (unless already changed)
            if ($imageId->getProductId() === $this) {
                $imageId->setProductId(null);
            }
        }

        return $this;
    }

    public function getCategoryId(): ?Category
    {
        return $this->category_id;
    }

    public function setCategoryId(Category $category_id): static
    {
        $this->category_id = $category_id;

        return $this;
    }

    public function getStockId(): ?string
    {
        return $this->stock_id;
    }

    public function setStockId(string $stock_id): static
    {
        $this->stock_id = $stock_id;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPegi(): ?int
    {
        return $this->pegi;
    }

    public function setPegi(int $pegi): static
    {
        $this->pegi = $pegi;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, Stock>
     */
    public function getProductId(): Collection
    {
        return $this->product_id;
    }

    public function addProductId(Stock $productId): static
    {
        if (!$this->product_id->contains($productId)) {
            $this->product_id->add($productId);
            $productId->setProductId($this);
        }

        return $this;
    }

    public function removeProductId(Stock $productId): static
    {
        if ($this->product_id->removeElement($productId)) {
            // set the owning side to null (unless already changed)
            if ($productId->getProductId() === $this) {
                $productId->setProductId(null);
            }
        }

        return $this;
    }

    public function getProductsId(): ?Order
    {
        return $this->products_id;
    }

    public function setProductsId(?Order $products_id): static
    {
        $this->products_id = $products_id;

        return $this;
    }
}
