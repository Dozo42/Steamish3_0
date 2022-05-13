<?php

namespace App\Entity;

use App\Repository\AccountRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[UniqueEntity(fields: 'email'), UniqueEntity(fields: 'name')]
#[ORM\Entity(repositoryClass: AccountRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class Account implements UserInterface, PasswordAuthenticatedUserInterface
{

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\Id, ORM\GeneratedValue('AUTO'), ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private string $name;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private string $email;

    #[ORM\Column(type: 'string', length: 180, nullable: true)]
    private ?string $nickname;

    #[ORM\Column(type: 'float')]
    private float $wallet;

    #[ORM\OneToMany(mappedBy: 'account', targetEntity: Library::class)]
    private Collection $libraries;

    #[ORM\OneToMany(mappedBy: 'account', targetEntity: Comment::class)]
    private Collection $comments;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $slug;

    #[ORM\Column(type: 'datetime')]
    protected ?DateTime $createdAt;

    #[ORM\OneToMany(mappedBy: 'createdBy', targetEntity: Topic::class)]
    private $topics;

    #[ORM\OneToMany(mappedBy: 'createdBy', targetEntity: Message::class)]
    private $messages;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $nbBanWord;

    #[ORM\OneToMany(mappedBy: 'createBy', targetEntity: DirectMessage::class)]
    private $messagesSent;

    #[ORM\OneToMany(mappedBy: 'receiver', targetEntity: DirectMessage::class)]
    private $messagesReceived;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $avatarFileName;

    public function __construct()
    {
        $this->libraries = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->wallet = 0.0;
        $this->topics = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->directMessages = new ArrayCollection();
        $this->messagesSent = new ArrayCollection();
        $this->messagesReceived = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(?string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getWallet(): ?float
    {
        return $this->wallet;
    }

    public function setWallet(float $wallet): self
    {
        $this->wallet = $wallet;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return DateTime|null
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime|null $createdAt
     */
    public function setCreatedAt(?DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return Collection
     */
    public function getLibraries(): Collection
    {
        return $this->libraries;
    }

    public function addLibrary(Library $library): self
    {
        if (!$this->libraries->contains($library)) {
            $this->libraries[] = $library;
        }

        return $this;
    }

    public function removeLibrary(Library $library): self
    {
        $this->libraries->removeElement($library);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        $this->comments->removeElement($comment);

        return $this;
    }

    public function getTotalGameTime(): int {
        $totalGameTime = 0;
        foreach ($this->getLibraries() as $library) {
            /** @var Library $library */
            $totalGameTime += $library->getGameTime();
        }
        return $totalGameTime;
    }

    public function getTotalPrice(): int {
        $totalPrice = 0;
        foreach ($this->getLibraries() as $library) {
            /** @var Library $library */
            $totalPrice += $library->getGame()->getPrice();
        }
        return $totalPrice;
    }

     /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Topic>
     */
    public function getTopics(): Collection
    {
        return $this->topics;
    }

    public function addTopic(Topic $topic): self
    {
        if (!$this->topics->contains($topic)) {
            $this->topics[] = $topic;
            $topic->setCreatedBy($this);
        }

        return $this;
    }

    public function removeTopic(Topic $topic): self
    {
        if ($this->topics->removeElement($topic)) {
            // set the owning side to null (unless already changed)
            if ($topic->getCreatedBy() === $this) {
                $topic->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setCreatedBy($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getCreatedBy() === $this) {
                $message->setCreatedBy(null);
            }
        }

        return $this;
    }

    public function getNbBanWord(): ?int
    {
        return $this->nbBanWord;
    }

    public function setNbBanWord(?int $nbBanWord): self
    {
        $this->nbBanWord = $nbBanWord;

        return $this;
    }

    public function incrementNbBanWord() {
        $this->nbBanWord += 1;
    }

    /**
     * @return Collection<int, DirectMessage>
     */
    public function getDirectMessages(): Collection
    {
        return $this->directMessages;
    }

    /**
     * @return Collection<int, DirectMessage>
     */
    public function getMessagesSent(): Collection
    {
        return $this->messagesSent;
    }

    public function addMessagesSent(DirectMessage $messagesSent): self
    {
        if (!$this->messagesSent->contains($messagesSent)) {
            $this->messagesSent[] = $messagesSent;
            $messagesSent->setCreateBy($this);
        }

        return $this;
    }

    public function removeMessagesSent(DirectMessage $messagesSent): self
    {
        if ($this->messagesSent->removeElement($messagesSent)) {
            // set the owning side to null (unless already changed)
            if ($messagesSent->getCreateBy() === $this) {
                $messagesSent->setCreateBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DirectMessage>
     */
    public function getMessagesReceived(): Collection
    {
        return $this->messagesReceived;
    }

    public function addMessagesReceived(DirectMessage $messagesReceived): self
    {
        if (!$this->messagesReceived->contains($messagesReceived)) {
            $this->messagesReceived[] = $messagesReceived;
            $messagesReceived->setReceiver($this);
        }

        return $this;
    }

    public function removeMessagesReceived(DirectMessage $messagesReceived): self
    {
        if ($this->messagesReceived->removeElement($messagesReceived)) {
            // set the owning side to null (unless already changed)
            if ($messagesReceived->getReceiver() === $this) {
                $messagesReceived->setReceiver(null);
            }
        }

        return $this;
    }

    public function getAvatarFileName(): ?string
    {
        return $this->avatarFileName;
    }

    public function setAvatarFileName(?string $avatarFileName): self
    {
        $this->avatarFileName = $avatarFileName;

        return $this;
    }
    
}