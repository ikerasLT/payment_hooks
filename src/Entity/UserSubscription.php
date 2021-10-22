<?php

namespace App\Entity;

use App\Repository\SubscriptionUserRepository;
use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SubscriptionUserRepository::class)
 */
class UserSubscription
{
    const STATUS_ACTIVE = 1;
    const STATUS_CANCELLED = 2;
    const STATUS_FAILED = 3;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity=Subscription::class, inversedBy="subscriptionUsers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $subscription;
    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="subscriptionUsers")
     */
    private $user;
    /**
     * @ORM\Column(type="datetime")
     */
    private $activeFrom;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $activeTo;
    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubscription(): ?Subscription
    {
        return $this->subscription;
    }

    public function setSubscription(?Subscription $subscription): self
    {
        $this->subscription = $subscription;

        return $this;
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

    public function getActiveFrom(): ?\DateTimeInterface
    {
        return $this->activeFrom;
    }

    public function setActiveFrom(\DateTimeInterface $activeFrom): self
    {
        $this->activeFrom = $activeFrom;

        return $this;
    }

    public function getActiveTo(): ?\DateTimeInterface
    {
        return $this->activeTo;
    }

    public function setActiveTo(?\DateTimeInterface $activeTo): self
    {
        $this->activeTo = $activeTo;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function isActive(): bool
    {
        $currentDate = Carbon::now();

        if ($this->activeFrom > $currentDate) {
            return false;
        }

        if ($this->activeTo < $currentDate) {
            return false;
        }

        return true;
    }
}
