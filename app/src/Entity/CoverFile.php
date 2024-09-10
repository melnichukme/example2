<?php

namespace App\Entity;

use App\Repository\CoverFileRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CoverFileRepository::class)]
class CoverFile
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: "cover_file", targetEntity: Cover::class)]
    #[ORM\JoinColumn(name: "cover_id", referencedColumnName: "id")]
    private ?Cover $cover = null;

    #[ORM\Column(type: Types::STRING, length: 64)]
    #[Groups(['default'])]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 64)]
    #[Groups(['default'])]
    private string $extension;

    #[ORM\Column(type: Types::STRING, length: 64)]
    #[Groups(['default'])]
    private string $path;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCover(): Cover
    {
        return $this->cover;
    }

    public function setCover(Cover $cover): void
    {
        $this->cover = $cover;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): void
    {
        $this->extension = $extension;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }
}
