<?php

namespace App\Entity;

use App\Repository\InvoiceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $invoiceNumber = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(type: "float")]
    private ?float $totalHT = null;
    #[ORM\Column(type: "float")]
    private ?float $totalTTC = null;
    #[ORM\Column(length: 255)]
    private ?string $paymentMethod = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $updatedAt = null;

    #[ORM\Column]
    private ?\DateTime $dueDate = null;

    #[ORM\Column( nullable: true)]
    private ?\DateTime $paymentDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $userValidate = null;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'invoices')]
    private ?Client $client = null;

    #[ORM\ManyToOne(targetEntity: Quote::class, inversedBy: 'invoices')]
    private ?Quote $quote = null;

    public function __construct() {
        $this->paymentMethod = "Non défini";
    }

    // Getters and Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInvoiceNumber(): ?string
    {
        return $this->invoiceNumber;
    }

    public function setInvoiceNumber(?string $invoiceNumber): void
    {
        $this->invoiceNumber = $invoiceNumber;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    public function getTotalHT(): ?float
    {
        return $this->totalHT;
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



    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(?string $paymentMethod): void
    {
        $this->paymentMethod = $paymentMethod;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getDueDate(): ?\DateTime
    {
        return $this->dueDate;
    }

    public function setDueDate(?\DateTime $dueDate): void
    {
        $this->dueDate = $dueDate;
    }

    public function getPaymentDate(): ?\DateTime
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(?\DateTime $paymentDate): void
    {
        $this->paymentDate = $paymentDate;
    }

    public function getUserValidate(): ?string
    {
        return $this->userValidate;
    }

    public function setUserValidate(?string $userValidate): void
    {
        $this->userValidate = $userValidate;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): void
    {
        $this->client = $client;
    }

    public function getQuote(): ?Quote
    {
        return $this->quote;
    }

    public function setQuote(?Quote $quote): void
    {
        $this->quote = $quote;
    }
}
