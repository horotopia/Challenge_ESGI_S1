<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmailTemplateRepository::class)]
class EmailTemplate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'text')]
    private ?string $contentBeforeButtons = null;

    #[ORM\Column(type: 'text')]
    private ?string $contentAfterButtons = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getContentBeforeButtons(): ?string
    {
        return $this->contentBeforeButtons;
    }

    public function setContentBeforeButtons(?string $contentBeforeButtons): void
    {
        $this->contentBeforeButtons = $contentBeforeButtons;
    }

    public function getContentAfterButtons(): ?string
    {
        return $this->contentAfterButtons;
    }

    public function setContentAfterButtons(?string $contentAfterButtons): void
    {
        $this->contentAfterButtons = $contentAfterButtons;
    }
}