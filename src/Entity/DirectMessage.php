<?php

namespace App\Entity;

use App\Repository\DirectMessageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DirectMessageRepository::class)]
class DirectMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'text')]
    private $content;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'boolean')]
    private $hasBeenSeen;

    #[ORM\ManyToOne(targetEntity: Account::class, inversedBy: 'messagesSent')]
    #[ORM\JoinColumn(nullable: false)]
    private $createBy;

    #[ORM\ManyToOne(targetEntity: Account::class, inversedBy: 'messagesReceived')]
    #[ORM\JoinColumn(nullable: false)]
    private $receiver;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getHasBeenSeen(): ?bool
    {
        return $this->hasBeenSeen;
    }

    public function setHasBeenSeen(bool $hasBeenSeen): self
    {
        $this->hasBeenSeen = $hasBeenSeen;

        return $this;
    }

    public function getCreateBy(): ?Account
    {
        return $this->createBy;
    }

    public function setCreateBy(?Account $createBy): self
    {
        $this->createBy = $createBy;

        return $this;
    }

    public function getReceiver(): ?Account
    {
        return $this->receiver;
    }

    public function setReceiver(?Account $receiver): self
    {
        $this->receiver = $receiver;

        return $this;
    }
}
