<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[UniqueEntity('slug')] // handles error in php otherwise sql error will be thrown
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['entity'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['entity'])]
    private ?string $name = null;

    #[ORM\Column(name: 'slug', type: 'string', length: 255, unique: true)]
    #[Assert\NotBlank]
    #[Groups(['entity'])]
    private ?string $slug = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank]
    #[Groups(['entity'])]
    private ?int $base_price = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['entity'])]
    private ?int $sale_price = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['entity'])]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?Color $color = null;

    /**
     * @var Collection<int, Store>
     */
    #[ORM\ManyToMany(targetEntity: Store::class, inversedBy: 'products_in_stock')]
    private Collection $in_stock_in_stores;

    public function __construct()
    {
        $this->in_stock_in_stores = new ArrayCollection();
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

    public function getBasePrice(): ?int
    {
        return $this->base_price;
    }

    public function setBasePrice(int $base_price): static
    {
        $this->base_price = $base_price;

        return $this;
    }

    public function getSalePrice(): ?int
    {
        return $this->sale_price;
    }

    public function setSalePrice(?int $sale_price): static
    {
        $this->sale_price = $sale_price;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getColor(): ?Color
    {
        return $this->color;
    }

    public function setColor(?Color $color): static
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return Collection<int, Store>
     */
    public function getInStockInStores(): Collection
    {
        return $this->in_stock_in_stores;
    }

    public function addInStockInStore(Store $inStockInStore): static
    {
        if (!$this->in_stock_in_stores->contains($inStockInStore)) {
            $this->in_stock_in_stores->add($inStockInStore);
        }

        return $this;
    }

    public function removeInStockInStore(Store $inStockInStore): static
    {
        $this->in_stock_in_stores->removeElement($inStockInStore);

        return $this;
    }
}
