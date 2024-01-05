<?php

declare(strict_types=1);

namespace App\Entity\Order;

use App\Entity\Licence\Licence;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Subscription\Subscription;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Core\Model\Order as BaseOrder;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_order")
 */
#[ORM\Entity]
#[ORM\Table(name: 'sylius_order')]
class Order extends BaseOrder
{
    #[ORM\OneToMany(mappedBy: 'syliusOrder', targetEntity: Licence::class)]
    private Collection $licences;

    #[ORM\OneToMany(mappedBy: 'syliusOrder', targetEntity: Subscription::class)]
    private Collection $subscriptions;

    public function __construct()
    {
        parent::__construct();
        $this->licences = new ArrayCollection();
        $this->subscriptions = new ArrayCollection();
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
            $licence->setSyliusOrder($this);
        }

        return $this;
    }

    public function removeLicence(Licence $licence): static
    {
        if ($this->licences->removeElement($licence)) {
            // set the owning side to null (unless already changed)
            if ($licence->getSyliusOrder() === $this) {
                $licence->setSyliusOrder(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Subscription>
     */
    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function addSubscription(Subscription $subscription): static
    {
        if (!$this->subscriptions->contains($subscription)) {
            $this->subscriptions->add($subscription);
            $subscription->setSyliusOrder($this);
        }

        return $this;
    }

    public function removeSubscription(Subscription $subscription): static
    {
        if ($this->subscriptions->removeElement($subscription)) {
            // set the owning side to null (unless already changed)
            if ($subscription->getSyliusOrder() === $this) {
                $subscription->setSyliusOrder(null);
            }
        }

        return $this;
    }
}
