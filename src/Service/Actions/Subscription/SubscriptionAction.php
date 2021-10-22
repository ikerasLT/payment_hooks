<?php

namespace App\Service\Actions\Subscription;

use App\Entity\Subscription;
use App\Entity\User;
use App\Entity\UserSubscription;
use App\Exceptions\ValidationException;
use App\Service\Actions\Transaction\Create as CreateTransaction;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

abstract class SubscriptionAction
{
    protected EntityManagerInterface $em;

    protected int $subscriptionId;
    protected int $userId;
    protected DateTime $activeFrom;
    protected DateTime $activeTo; //maybe should be resolved from subscription object
    protected string $externalId;
    protected string $provider;
    protected int $quantity;
    protected int $amount;
    protected DateTime $purchasedAt;
    private CreateTransaction $createTransaction;

    public function __construct(CreateTransaction $createTransaction, EntityManagerInterface $em)
    {
        $this->createTransaction = $createTransaction;
        $this->em = $em;
    }

    public function getSubscriptionId(): int
    {
        return $this->subscriptionId;
    }

    public function setSubscriptionId(int $subscriptionId): self
    {
        $this->subscriptionId = $subscriptionId;

        return $this;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getActiveFrom(): DateTime
    {
        return $this->activeFrom;
    }

    public function setActiveFrom(DateTime $activeFrom): self
    {
        $this->activeFrom = $activeFrom;

        return $this;
    }

    public function getActiveTo(): DateTime
    {
        return $this->activeTo;
    }

    public function setActiveTo(DateTime $activeTo): self
    {
        $this->activeTo = $activeTo;

        return $this;
    }

    public function getExternalId(): string
    {
        return $this->externalId;
    }

    public function setExternalId(string $externalId): self
    {
        $this->externalId = $externalId;

        return $this;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function setProvider(string $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getPurchasedAt(): DateTime
    {
        return $this->purchasedAt;
    }

    public function setPurchasedAt(DateTime $purchasedAt): self
    {
        $this->purchasedAt = $purchasedAt;

        return $this;
    }

    public function setData(array $data): self
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }

        return $this;
    }

    protected function getUser(): User
    {
        $user = $this->em->find(User::class, $this->userId);

        if (! $user) {
            throw new ValidationException();
        }

        return $user;
    }

    protected function getSubscription(): Subscription
    {
        $subscription = $this->em->find(User::class, $this->subscriptionId);

        if (! $subscription) {
            throw new ValidationException();
        }

        return $subscription;
    }

    protected function addTransaction(User $user, Subscription $subscription, bool $success = true): void
    {
        $this->createTransaction
            ->setUser($user)
            ->setSubscription($subscription)
            ->setExternalId($this->externalId)
            ->setProvider($this->provider)
            ->setAmount($this->amount)
            ->setPurchasedAt($this->purchasedAt)
            ->setQuantity($this->quantity)
            ->setSuccessful($success)
            ->handle();
    }

    protected function basicValidation(): bool
    {
        //TODO Validate existence datatypes and other basic rules
    }

    protected function getUserSubscription(User $user, Subscription $subscription): UserSubscription
    {
        $userSubscription = $this->em
            ->getRepository(UserSubscription::class)
            ->findOneBy(compact('user', 'subscription'));

        if (! $userSubscription) {
            throw new ValidationException();
        }

        return $userSubscription;
    }

    protected function persist()
    {
        $this->em->flush();
    }
}
