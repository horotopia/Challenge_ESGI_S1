<?php

namespace App\Entity;

use App\Repository\FactureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FactureRepository::class)]
class Facture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 4)]
    private ?string $montant = null;

    #[ORM\Column(length: 255)]
    private ?string $methode_paiement = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $create_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $update_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_echeance = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_paiement = null;

    #[ORM\Column(length: 255)]
    private ?string $user_validate = null;

    #[ORM\ManyToOne(inversedBy: 'factures')]
    private ?client $id_client = null;

    #[ORM\ManyToOne(inversedBy: 'factures')]
    private ?Devis $id_devis = null;

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

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getMethodePaiement(): ?string
    {
        return $this->methode_paiement;
    }

    public function setMethodePaiement(string $methode_paiement): static
    {
        $this->methode_paiement = $methode_paiement;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeImmutable
    {
        return $this->create_at;
    }

    public function setCreateAt(\DateTimeImmutable $create_at): static
    {
        $this->create_at = $create_at;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeImmutable
    {
        return $this->update_at;
    }

    public function setUpdateAt(\DateTimeImmutable $update_at): static
    {
        $this->update_at = $update_at;

        return $this;
    }

    public function getDateEcheance(): ?\DateTimeInterface
    {
        return $this->date_echeance;
    }

    public function setDateEcheance(\DateTimeInterface $date_echeance): static
    {
        $this->date_echeance = $date_echeance;

        return $this;
    }

    public function getDatePaiement(): ?\DateTimeInterface
    {
        return $this->date_paiement;
    }

    public function setDatePaiement(\DateTimeInterface $date_paiement): static
    {
        $this->date_paiement = $date_paiement;

        return $this;
    }

    public function getUserValidate(): ?string
    {
        return $this->user_validate;
    }

    public function setUserValidate(string $user_validate): static
    {
        $this->user_validate = $user_validate;

        return $this;
    }

    public function getIdClient(): ?client
    {
        return $this->id_client;
    }

    public function setIdClient(?client $id_client): static
    {
        $this->id_client = $id_client;

        return $this;
    }

    public function getIdDevis(): ?Devis
    {
        return $this->id_devis;
    }

    public function setIdDevis(?Devis $id_devis): static
    {
        $this->id_devis = $id_devis;

        return $this;
    }
}
