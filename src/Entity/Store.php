<?php

namespace App\Entity;

use App\Repository\StoreRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: StoreRepository::class)]
class Store
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $uuid = null;

    #[ORM\Column(length: 255)]
    private ?string $storeName = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $openingTime = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $closingTime = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToMany(targetEntity: Product::class, mappedBy: 'stores')]
    private Collection $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
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

    public function __toString(): string
    {
        return $this->storeName; // Remplacez par le champ approprié (par exemple, name)
    }

    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->addStore($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            $product->removeStore($this);
        }

        return $this;
    }
    
    public function getStoreName(): ?string
    {
        return $this->storeName;
    }

    public function setStoreName(string $storeName): static
    {
        $this->storeName = $storeName;

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

    public function getOpeningTime(): ?\DateTimeImmutable
    {
        return $this->openingTime;
    }

    public function setOpeningTime(?\DateTimeImmutable $openingTime): static
    {
        $this->openingTime = $openingTime;

        return $this;
    }

    public function getClosingTime(): ?\DateTimeImmutable
    {
        return $this->closingTime;
    }

    public function setClosingTime(?\DateTimeImmutable $closingTime): static
    {
        $this->closingTime = $closingTime;

        return $this;
    }


    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
