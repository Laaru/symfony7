<?php

namespace App\Entity;

use App\Repository\StoreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: StoreRepository::class)]
class Store
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['entity'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['entity'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['entity'])]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['entity'])]
    private ?string $address = null;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\ManyToMany(targetEntity: Product::class, mappedBy: 'in_stock_in_stores')]
    private Collection $products_in_stock;

    public function __construct()
    {
        $this->products_in_stock = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProductsInStock(): Collection
    {
        return $this->products_in_stock;
    }

    public function addProductsInStock(Product $productsInStock): static
    {
        if (!$this->products_in_stock->contains($productsInStock)) {
            $this->products_in_stock->add($productsInStock);
            $productsInStock->addInStockInStore($this);
        }

        return $this;
    }

    public function removeProductsInStock(Product $productsInStock): static
    {
        if ($this->products_in_stock->removeElement($productsInStock)) {
            $productsInStock->removeInStockInStore($this);
        }

        return $this;
    }
}
