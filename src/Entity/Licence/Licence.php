<?php

namespace App\Entity\Licence;

use App\Entity\Order\Order;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\LicenceRepository;
use App\Entity\Product\ProductVariant;
use Sylius\Component\Resource\Model\ResourceInterface;

#[ORM\Entity(repositoryClass: LicenceRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Licence implements ResourceInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $startedAt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $endedAt = null;

    #[ORM\Column(length: 255)]
    private ?string $platform = null;

    #[ORM\Column]
    private ?bool $demo = null;

    #[ORM\Column(length: 255)]
    private ?string $state = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $codeCrm = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'licences')]
    private ?Order $syliusOrder = null;

    #[ORM\ManyToOne(inversedBy: 'licences')]
    private ?ProductVariant $syliusProductVariant = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartedAt(): ?\DateTimeInterface
    {
        return $this->startedAt;
    }

    public function setStartedAt(\DateTimeInterface $startedAt): static
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getEndedAt(): ?\DateTimeInterface
    {
        return $this->endedAt;
    }

    public function setEndedAt(\DateTimeInterface $endedAt): static
    {
        $this->endedAt = $endedAt;

        return $this;
    }

    public function getPlatform(): ?string
    {
        return $this->platform;
    }

    public function setPlatform(string $platform): static
    {
        $this->platform = $platform;

        return $this;
    }

    public function isDemo(): ?bool
    {
        return $this->demo;
    }

    public function setDemo(bool $demo): static
    {
        $this->demo = $demo;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getCodeCrm(): ?string
    {
        return $this->codeCrm;
    }

    public function setCodeCrm(?string $codeCrm): static
    {
        $this->codeCrm = $codeCrm;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = new \DateTimeImmutable();;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getSyliusOrder(): ?Order
    {
        return $this->syliusOrder;
    }

    public function setSyliusOrder(?Order $syliusOrder): static
    {
        $this->syliusOrder = $syliusOrder;

        return $this;
    }

    public function getSyliusProductVariant(): ?ProductVariant
    {
        return $this->syliusProductVariant;
    }

    public function setSyliusProductVariant(?ProductVariant $syliusProductVariant): static
    {
        $this->syliusProductVariant = $syliusProductVariant;

        return $this;
    }
}
