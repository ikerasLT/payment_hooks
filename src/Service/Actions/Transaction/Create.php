<?php

namespace App\Service\Actions\Transaction;

use App\Entity\Subscription;
use App\Entity\Transaction;
use App\Entity\User;
use App\Service\Actions\EntityAction;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class Create extends EntityAction
{
    private EntityManagerInterface $em;

    private User $user;
    private Subscription $subscription;
    private string $externalId;
    private string $provider;
    private int $quantity;
    private int $amount;
    private DateTime $purchasedAt;
    private bool $successful;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getSubscription(): Subscription
    {
        return $this->subscription;
    }

    public function setSubscription(Subscription $subscription): self
    {
        $this->subscription = $subscription;

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

    public function isSuccessful(): bool
    {
        return $this->successful;
    }

    public function setSuccessful(bool $successful): self
    {
        $this->successful = $successful;

        return $this;
    }

    protected function validate(): void
    {
        // TODO: Implement validate() method.
    }

    protected function getObject()
    {
        return new Transaction();
    }

    /**
     * @param Transaction $object
     */
    protected function fillData($object): void
    {
        $status = $this->successful ? Transaction::STATUS_SUCCESS : Transaction::STATUS_FAILURE;

        $object->setUser($this->user)
            ->setSubscription($this->subscription)
            ->setStatus($status)
            ->setQuantity($this->quantity)
            ->setPurchasedAt($this->purchasedAt)
            ->setProvider($this->provider)
            ->setExternalId($this->externalId);
    }

    protected function persist($object): void
    {
        $this->em->persist($object);
    }
}
