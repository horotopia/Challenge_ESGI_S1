<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $marque = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 4)]
    private ?string $prix_unitaire = null;

    #[ORM\Column]
    private ?int $tva = null;

    #[ORM\Column]
    private ?int $quantite_disponible = null;

    #[ORM\Column]
    private ?\DateTime $create_at = null;

    #[ORM\Column]
    private ?\DateTime $update_at = null;

    #[ORM\Column(length: 255)]
    private ?string $user_create = null;

    #[ORM\Column(length: 255)]
    private ?string $user_update = null;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    private ?Categorie $id_categorie = null;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    private ?Entreprise $id_entreprise = null;

    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: DevisProduit::class, cascade: ['persist', 'remove'])]
    private Collection $devisProduits;
    public function __construct()
    {
        $this->devis = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

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

    public function getMarque(): ?string
    {
        return $this->marque;
    }

    public function setMarque(string $marque): static
    {
        $this->marque = $marque;

        return $this;
    }

    public function getPrixUnitaire(): ?string
    {
        return $this->prix_unitaire;
    }

    public function setPrixUnitaire(string $prix_unitaire): static
    {
        $this->prix_unitaire = $prix_unitaire;

        return $this;
    }

    public function getTva(): ?int
    {
        return $this->tva;
    }

    public function setTva(int $tva): static
    {
        $this->tva = $tva;

        return $this;
    }

    public function getQuantiteDisponible(): ?int
    {
        return $this->quantite_disponible;
    }

    public function setQuantiteDisponible(int $quantite_disponible): static
    {
        $this->quantite_disponible = $quantite_disponible;

        return $this;
    }

    public function getCreateAt(): ?\DateTime
    {
        return $this->create_at;
    }

    public function setCreateAt(\DateTime $create_at): static
    {
        $this->create_at = $create_at;

        return $this;
    }

    public function getUpdateAt(): ?\DateTime
    {
        return $this->update_at;
    }

    public function setUpdateAt(\DateTime $update_at): static
    {
        $this->update_at = $update_at;

        return $this;
    }

    public function getUserCreate(): ?string
    {
        return $this->user_create;
    }

    public function setUserCreate(string $user_create): static
    {
        $this->user_create = $user_create;

        return $this;
    }

    public function getUserUpdate(): ?string
    {
        return $this->user_update;
    }

    public function setUserUpdate(string $user_update): static
    {
        $this->user_update = $user_update;

        return $this;
    }

    public function getIdCategorie(): ?Categorie
    {
        return $this->id_categorie;
    }

    public function setIdCategorie(?Categorie $id_categorie): static
    {
        $this->id_categorie = $id_categorie;

        return $this;
    }

    public function getIdEntreprise(): ?Entreprise
    {
        return $this->id_entreprise;
    }

    public function setIdEntreprise(?Entreprise $id_entreprise): static
    {
        $this->id_entreprise = $id_entreprise;

        return $this;
    }

    /**
     * @return Collection<int, DevisProduit>
     */
    public function getDevisProduits(): Collection
    {
        return $this->devisProduits;
    }

    public function addDevisProduit(DevisProduit $devisProduit): static
    {
        if (!$this->devisProduits->contains($devisProduit)) {
            $this->devisProduits->add($devisProduit);
            $devisProduit->setProduit($this);
        }

        return $this;
    }

    public function removeDevisProduit(DevisProduit $devisProduit): static
    {
        $this->devisProduits->removeElement($devisProduit);

        return $this;
    }
}
