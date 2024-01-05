<?php

declare(strict_types=1);

namespace App\Entity\Product;

use App\Entity\Licence\Licence;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\ProductVariant as BaseProductVariant;
use Sylius\Component\Product\Model\ProductVariantTranslationInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_product_variant")
 */
#[ORM\Entity]
#[ORM\Table(name: 'sylius_product_variant')]
class ProductVariant extends BaseProductVariant
{
    #[ORM\OneToMany(mappedBy: 'syliusProductVariant', targetEntity: Licence::class)]
    private Collection $licences;

    #[ORM\Column]
    private ?int $durationValue = null;

    #[ORM\Column(length: 255)]
    private ?string $durationUnit = null;

    #[ORM\Column]
    private ?bool $isRecurring = null;

    public function __construct()
    {
        parent::__construct();
        $this->licences = new ArrayCollection();
    }

    protected function createTranslation(): ProductVariantTranslationInterface
    {
        return new ProductVariantTranslation();
    }

    /**
     * @return Collection<int, Licence>
     */
    public function getLicences(): Collection
    {
        return $this->licences;
    }

    public function addLicence(Licence $licence): static
    {
        if (!$this->licences->contains($licence)) {
            $this->licences->add($licence);
            $licence->setSyliusProductVariant($this);
        }

        return $this;
    }

    public function removeLicence(Licence $licence): static
    {
        if ($this->licences->removeElement($licence)) {
            // set the owning side to null (unless already changed)
            if ($licence->getSyliusProductVariant() === $this) {
                $licence->setSyliusProductVariant(null);
            }
        }

        return $this;
    }

    public function getDurationValue(): ?int
    {
        return $this->durationValue;
    }

    public function setDurationValue(?int $durationValue): static
    {
        $this->durationValue = $durationValue;

        return $this;
    }

    public function getDurationUnit(): ?string
    {
        return $this->durationUnit;
    }

    public function setDurationUnit(?string $durationUnit): static
    {
        $this->durationUnit = $durationUnit;

        return $this;
    }

    public function getIsRecurring(): ?bool
    {
        return $this->isRecurring;
    }

    public function setIsRecurring(bool $isRecurring): static
    {
        $this->isRecurring = $isRecurring;

        return $this;
    }

}
