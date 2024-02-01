<?php



namespace App\Entity;
use App\Repository\DevisProduitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class DevisProduit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\ManyToOne(targetEntity: Devis::class, inversedBy: ' devisProduits')]
    private ?Devis $devis;

    #[ORM\ManyToOne(targetEntity: Produit::class, inversedBy: 'devisProduits')]
    private ?Produit $produit;

    // Getters and Setters for other properties...

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(?int $quantite): void
    {
        $this->quantite = $quantite;
    }

    public function getDevis(): ?Devis
    {
        return $this->devis;
    }

    public function setDevis(?Devis $devis): void
    {
        $this->devis = $devis;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): void
    {
        $this->produit = $produit;
    }
}
