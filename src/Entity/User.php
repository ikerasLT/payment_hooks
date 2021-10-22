<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=UserSubscription::class, mappedBy="users")
     */
    private $subscriptionUsers;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="users", orphanRemoval=true)
     */
    private $transactions;

    public function __construct()
    {
        $this->subscriptionUsers = new ArrayCollection();
        $this->transactions = new ArrayCollection();
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
            $subscriptionUser->setUser($this);
        }

        return $this;
    }

    public function removeSubscriptionUser(UserSubscription $subscriptionUser): self
    {
        if ($this->subscriptionUsers->removeElement($subscriptionUser)) {
            // set the owning side to null (unless already changed)
            if ($subscriptionUser->getUser() === $this) {
                $subscriptionUser->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setUser($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getUser() === $this) {
                $transaction->setUser(null);
            }
        }

        return $this;
    }
}
