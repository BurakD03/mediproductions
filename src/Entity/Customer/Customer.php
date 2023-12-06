<?php

declare(strict_types=1);

namespace App\Entity\Customer;

use App\Entity\Office;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\Customer as BaseCustomer;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_customer")
 */
#[ORM\Entity]
#[ORM\Table(name: 'sylius_customer')]
class Customer extends BaseCustomer
{
    #[ORM\OneToMany(mappedBy: 'user_id', targetEntity: Office::class)]
    private Collection $offices;

    public function __construct()
    {
        parent::__construct();
        $this->offices = new ArrayCollection();
    }

    /**
     * @return Collection<int, Office>
     */
    public function getOffices(): Collection
    {
        return $this->offices;
    }

    public function addOffice(Office $office): static
    {
        if (!$this->offices->contains($office)) {
            $this->offices->add($office);
            $office->setUserId($this);
        }

        return $this;
    }

    public function removeOffice(Office $office): static
    {
        if ($this->offices->removeElement($office)) {
            // set the owning side to null (unless already changed)
            if ($office->getUserId() === $this) {
                $office->setUserId(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getFullName();
    }
}
