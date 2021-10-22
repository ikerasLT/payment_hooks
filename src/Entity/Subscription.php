<?php

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SubscriptionRepository::class)
 */
class Subscription
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=UserSubscription::class, mappedBy="subscription", orphanRemoval=true)
     */
    private $subscriptionUsers;

    public function __construct()
    {
        $this->subscriptionUsers = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|UserSubscription[]
     */
    public function getSubscriptionUsers(): Collection
    {
        return $this->subscriptionUsers;
    }

    public function addSubscriptionUser(UserSubscription $subscriptionUser): self
    {
        if (!$this->subscriptionUsers->contains($subscriptionUser)) {
            $this->subscriptionUsers[] = $subscriptionUser;
            $subscriptionUser->setSubscription($this);
        }

        return $this;
    }

    public function removeSubscriptionUser(UserSubscription $subscriptionUser): self
    {
        if ($this->subscriptionUsers->removeElement($subscriptionUser)) {
            // set the owning side to null (unless already changed)
            if ($subscriptionUser->getSubscription() === $this) {
                $subscriptionUser->setSubscription(null);
            }
        }

        return $this;
    }

}
