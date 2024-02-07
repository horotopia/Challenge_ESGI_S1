<?php

namespace App\Entity;

use App\Repository\DevisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DevisRepository::class)]
class Devis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column(length: 255)]
    private ?string $num_devis = null;
    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\Column]
    private ?float $totalHT = null;
    #[ORM\Column]
    private ?float $totalTTC = null;



    #[ORM\Column]
    private ?int $tranches = null;

    #[ORM\Column]
    private ?\DateTime $create_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime$update_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $date_echeance = null;

    #[ORM\Column(length: 255)]
    private ?string $user_create = null;

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $user_update = null;

    #[ORM\ManyToOne(inversedBy: 'devis')]
    private ?Client $id_client = null;

    #[ORM\OneToMany(mappedBy: 'devis', targetEntity: DevisProduit::class, cascade: ['persist', 'remove'])]
    private Collection $devisProduits;

    #[ORM\OneToMany(mappedBy: 'id_devis', targetEntity: Facture::class)]
    private Collection $factures;

    public function __construct()
    {
        $this->devisProduits= new ArrayCollection();
        $this->factures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getTotalHT(): ?float
    {
        return $this->totalHT;
    }

    public function getNumDevis(): ?string
    {
        return $this->num_devis;
    }

    public function setNumDevis(?string $num_devis): void
    {
        $this->num_devis = $num_devis;
    }

    public function setTotalHT(?float $totalHT): void
    {
        $this->totalHT = $totalHT;
    }

    public function getTotalTTC(): ?float
    {
        return $this->totalTTC;
    }

    public function setTotalTTC(?float $totalTTC): void
    {
        $this->totalTTC = $totalTTC;
    }





    public function getTranches(): ?int
    {
        return $this->tranches;
    }

    public function setTranches(int $tranches): static
    {
        $this->tranches = $tranches;

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

    public function getDateEcheance(): ?\DateTime
    {
        return $this->date_echeance;
    }

    public function setDateEcheance(\DateTime $date_echeance): static
    {
        $this->date_echeance = $date_echeance;

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

    public function getIdClient(): ?Client
    {
        return $this->id_client;
    }

    public function setIdClient(?Client $id_client): static
    {
        $this->id_client = $id_client;

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
            $devisProduit->setDevis($this);
        }

        return $this;
    }

    public function removeDevisProduit(DevisProduit $devisProduit): static
    {
        $this->devisProduits->removeElement($devisProduit);

        return $this;
    }

    /**
     * @return Collection<int, Facture>
     */
    public function getFactures(): Collection
    {
        return $this->factures;
    }

    public function addFacture(Facture $facture): static
    {
        if (!$this->factures->contains($facture)) {
            $this->factures->add($facture);
            $facture->setIdDevis($this);
        }

        return $this;
    }

    public function removeFacture(Facture $facture): static
    {
        if ($this->factures->removeElement($facture)) {
            // set the owning side to null (unless already changed)
            if ($facture->getIdDevis() === $this) {
                $facture->setIdDevis(null);
            }
        }

        return $this;
    }
}
