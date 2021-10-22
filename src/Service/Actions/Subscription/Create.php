<?php

namespace App\Service\Actions\Subscription;

use App\Entity\Subscription;
use App\Entity\User;
use App\Entity\UserSubscription;
use App\Exceptions\ApplicationException;
use App\Exceptions\ValidationException;
use App\Service\Actions\Action;

class Create extends SubscriptionAction implements Action
{
    public function handle(): void
    {
        $user = $this->getUser();
        $subscription = $this->getSubscription();

        $this->validate($user, $subscription);

        $this->createUserSubscription($user, $subscription);
        $this->addTransaction($user, $subscription);

        $this->persist();

        $this->validateSuccess();
    }

    private function validate(User $user, Subscription $subscription): void
    {
        if (! $this->basicValidation()) {
            throw new ValidationException();
        }

        if (! $subscription->isPurchaseable()) {
            throw new ValidationException();
        }
    }

    private function createUserSubscription(User $user, Subscription $subscription): void
    {
        $userSubscription = new UserSubscription();
        $userSubscription->setSubscription($subscription)
            ->setUser($user)
            ->setActiveFrom($this->activeFrom)
            ->setActiveTo($this->activeTo)
            ->setStatus(UserSubscription::STATUS_ACTIVE);

        $this->em->persist($userSubscription);
    }

    private function validateSuccess(): void
    {
        if ($this->userSubscriptionMissing()) {
            throw new ApplicationException('Failed to create user subscription');
        }
    }
}
