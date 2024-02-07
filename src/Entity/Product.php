<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $brand = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 4)]
    private ?string $unitPrice = null;

    #[ORM\Column]
    private ?int $VAT = null;

    #[ORM\Column]
    private ?int $availableQuantity = null;

    #[ORM\Column]
    private ?\DateTime $createdAt = null;

    #[ORM\Column]
    private ?\DateTime $updatedAt = null;

    #[ORM\Column(length: 255)]
    private ?string $userCreated = null;

    #[ORM\Column(length: 255)]
    private ?string $userUpdated = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?Category $categoryId = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?Company $companyId = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: QuoteProduct::class, cascade: ['persist', 'remove'])]
    private Collection $quoteProducts;
    public function __construct()
    {
        $this->quote = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getUnitPrice(): ?string
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(string $unitPrice): static
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    public function getVAT(): ?int
    {
        return $this->VAT;
    }

    public function setVAT(int $VAT): static
    {
        $this->VAT = $VAT;

        return $this;
    }

    public function getAvailableQuantity(): ?int
    {
        return $this->availableQuantity;
    }

    public function setAvailableQuantity(int $availableQuantity): static
    {
        $this->availableQuantity = $availableQuantity;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUserCreated(): ?string
    {
        return $this->userCreated;
    }

    public function setUserCreated(string $userCreated): static
    {
        $this->userCreated = $userCreated;

        return $this;
    }

    public function getUserUpdated(): ?string
    {
        return $this->userUpdated;
    }

    public function setUserUpdated(string $userUpdated): static
    {
        $this->userUpdated = $userUpdated;

        return $this;
    }

    public function getCategoryId(): ?Category
    {
        return $this->categoryId;
    }

    public function setCategoryId(?Category $categoryId): static
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    public function getCompanyId(): ?Company
    {
        return $this->companyId;
    }

    public function setCompanyId(?Company $companyId): static
    {
        $this->companyId = $companyId;

        return $this;
    }

    /**
     * @return Collection<int, QuoteProduct>
     */
    public function getQuoteProducts(): Collection
    {
        return $this->quoteProducts;
    }

    public function addQuoteProduct(QuoteProduct $quoteProduct): static
    {
        if (!$this->quoteProducts->contains($quoteProduct)) {
            $this->quoteProducts->add($quoteProduct);
            $quoteProduct->setProduct($this);
        }

        return $this;
    }

    public function removeQuoteProduct(QuoteProduct $quoteProduct): static
    {
        $this->quoteProducts->removeElement($quoteProduct);

        return $this;
    }
}
