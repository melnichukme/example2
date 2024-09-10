<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
#[ORM\Table]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'notifies')]
    protected ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $role = null;

    #[ORM\Column(length: 255)]
    protected ?string $type = null;

    #[ORM\Column(length: 255)]
    protected ?string $entityId = null;

    #[ORM\Column]
    #[Groups(['default'])]
    protected array $payload = [];

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['default'])]
    protected ?\DateTimeInterface $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'notification', targetEntity: NotificationRead::class)]
    protected Collection $notificationReads;

    public function __construct()
    {
        $this->notificationReads = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getEntityId(): ?string
    {
        return $this->entityId;
    }

    public function setEntityId(string $entityId): self
    {
        $this->entityId = $entityId;

        return $this;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function setPayload(array $payload): self
    {
        $this->payload = $payload;

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

    /**
     * @return Collection<int, NotificationRead>
     */
    public function getNotificationReads(): Collection
    {
        return $this->notificationReads;
    }

    public function addNotificationRead(NotificationRead $notificationRead): self
    {
        if (!$this->notificationReads->contains($notificationRead)) {
            $this->notificationReads->add($notificationRead);
            $notificationRead->setNotification($this);
        }

        return $this;
    }

    public function removeNotificationRead(NotificationRead $notificationRead): self
    {
        if ($this->notificationReads->removeElement($notificationRead)) {
            // set the owning side to null (unless already changed)
            if ($notificationRead->getNotification() === $this) {
                $notificationRead->setNotification(null);
            }
        }

        return $this;
    }
}
