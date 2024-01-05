<?php

namespace App\Entity\Subscription;

use App\Entity\Order\Order;
use App\Entity\SubscriptionPaymentSchedule\SubscriptionPaymentSchedule;
use Doctrine\DBAL\Types\Types;
use App\Entity\Licence\Licence;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\PrePersist;
use App\Repository\SubscriptionRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Resource\Model\ResourceInterface;

#[ORM\Entity(repositoryClass: SubscriptionRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Subscription implements ResourceInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $state = SubscriptionStates::STATE_ACTIVE;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column]
    private ?int $currentPayment = null;

    #[ORM\Column]
    private ?int $totalPayment = null;

    #[ORM\ManyToOne(inversedBy: 'subscriptions')]
    private ?Order $syliusOrder = null;

    #[ORM\OneToMany(mappedBy: 'subscription', targetEntity: Licence::class)]
    private Collection $licence;

    #[ORM\OneToMany(mappedBy: 'subscription', targetEntity: SubscriptionPaymentSchedule::class)]
    private Collection $subscriptionPaymentSchedules;

    #[ORM\Column]
    private ?bool $isExtended = null;

    public function __construct()
    {
        $this->licence = new ArrayCollection();
        $this->subscriptionPaymentSchedules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): static
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAt(): static
    {
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getCurrentPayment(): ?int
    {
        return $this->currentPayment;
    }

    public function setCurrentPayment(int $currentPayment): static
    {
        $this->currentPayment = $currentPayment;

        return $this;
    }

    public function getTotalPayment(): ?int
    {
        return $this->totalPayment;
    }

    public function setTotalPayment(int $totalPayment): static
    {
        $this->totalPayment = $totalPayment;

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

    /**
     * @return Collection<int, Licence>
     */
    public function getLicence(): Collection
    {
        return $this->licence;
    }

    public function addLicence(Licence $licence): static
    {
        if (!$this->licence->contains($licence)) {
            $this->licence->add($licence);
            $licence->setSubscription($this);
        }

        return $this;
    }

    public function removeLicence(Licence $licence): static
    {
        if ($this->licence->removeElement($licence)) {
            // set the owning side to null (unless already changed)
            if ($licence->getSubscription() === $this) {
                $licence->setSubscription(null);
            }
        }

        return $this;
    }

    public function getEcheances(): string
    {
        return $this->getCurrentPayment() . ' / ' . $this->getTotalPayment();
    }

    /**
     * @return Collection<int, SubscriptionPaymentSchedule>
     */
    public function getSubscriptionPaymentSchedules(): Collection
    {
        return $this->subscriptionPaymentSchedules;
    }

    public function addSubscriptionPaymentSchedule(SubscriptionPaymentSchedule $subscriptionPaymentSchedule): static
    {
        if (!$this->subscriptionPaymentSchedules->contains($subscriptionPaymentSchedule)) {
            $this->subscriptionPaymentSchedules->add($subscriptionPaymentSchedule);
            $subscriptionPaymentSchedule->setSubscription($this);
        }

        return $this;
    }

    public function removeSubscriptionPaymentSchedule(SubscriptionPaymentSchedule $subscriptionPaymentSchedule): static
    {
        if ($this->subscriptionPaymentSchedules->removeElement($subscriptionPaymentSchedule)) {
            // set the owning side to null (unless already changed)
            if ($subscriptionPaymentSchedule->getSubscription() === $this) {
                $subscriptionPaymentSchedule->setSubscription(null);
            }
        }

        return $this;
    }

    public function getIsExtended(): ?bool
    {
        return $this->isExtended;
    }

    public function setIsExtended(bool $isExtended): static
    {
        $this->isExtended = $isExtended;

        return $this;
    }

    public function getRecurringOrder(): ?Order
    {
        $recurringOrder = new Order();
        
        if (null !== $this->syliusOrder) {
            foreach ($this->syliusOrder->getItems() as $orderItem) {
                $variant = $orderItem->getVariant();
                
                // Vérifie si le variant a isRecurring égal à 1
                if ($variant !== null && $variant->getIsRecurring() === 1) {
                    // Clone l'item et l'ajoute à la nouvelle commande
                    $recurringOrderItem = clone $orderItem;
                    $recurringOrder->addItem($recurringOrderItem);
                }
            }
        }

        return $recurringOrder;
    }
}
