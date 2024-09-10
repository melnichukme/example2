<?php

namespace App\Entity;

use App\Repository\ReleaseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: ReleaseRepository::class)]
class Release
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Media::class, inversedBy: 'releases')]
    #[Groups(['default'])]
    private ?Media $media = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true, options: ['unsigned' => true])]
    #[Groups(['default'])]
    private ?int $platform = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['default'])]
    private ?string $code;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['default'])]
    #[SerializedName('digital_at')]
    private ?\DateTimeInterface $digitalAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(?Media $media): void
    {
        $this->media = $media;
    }

    public function getPlatform(): ?int
    {
        return $this->platform;
    }

    public function setPlatform(?int $platform): void
    {
        $this->platform = $platform;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    public function getDigitalAt(): ?\DateTimeInterface
    {
        return $this->digitalAt;
    }

    public function setDigitalAt(?\DateTimeInterface $digitalAt): void
    {
        $this->digitalAt = $digitalAt;
    }
}
