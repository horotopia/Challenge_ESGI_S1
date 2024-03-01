<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: 'App\Repository\EmailLogRepository')]
class EmailLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $subject = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $sender = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $receiver = null;

    #[ORM\Column(type: 'text')]
    private ?string $content = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $status = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTime $sentAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $openedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    public function getSender(): ?string
    {
        return $this->sender;
    }

    public function setSender(string $sender): self
    {
        $this->sender = $sender;
        return $this;
    }

    public function getReceiver(): ?string
    {
        return $this->receiver;
    }

    public function setReceiver(string $receiver): self
    {
        $this->receiver = $receiver;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getSentAt(): ?\DateTime
    {
        return $this->sentAt;
    }

    public function setSentAt(\DateTime $sentAt): self
    {
        $this->sentAt = $sentAt;
        return $this;
    }

    public function getOpenedAt(): ?\DateTime
    {
        return $this->openedAt;
    }

    public function setOpenedAt(?\DateTime $openedAt): self
    {
        $this->openedAt = $openedAt;
        return $this;
    }
}