<?php



namespace App\Entity;
use App\Repository\QuoteProductRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class QuoteProduct
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\ManyToOne(targetEntity: Quote::class, inversedBy: ' quoteProducts')]
    private ?Quote $quote;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'quoteProducts')]
    private ?Product $product;

    // Getters and Setters for other properties...

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getQuote(): ?Quote
    {
        return $this->quote;
    }

    public function setQuote(?Quote $quote): void
    {
        $this->quote = $quote;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): void
    {
        $this->product = $product;
    }
}
