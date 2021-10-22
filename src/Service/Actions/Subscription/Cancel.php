<?php

namespace App\Service\Actions\Subscription;

use App\Entity\UserSubscription;
use App\Exceptions\ApplicationException;
use App\Exceptions\ValidationException;
use App\Service\Actions\Action;

class Cancel extends SubscriptionAction implements Action
{
    public function handle(): void
    {
        $user = $this->getUser();
        $subscription = $this->getSubscription();
        $userSubscription = $this->getUserSubscription($user, $subscription);

        $this->validate($userSubscription);

        $this->updateSubscriptionStatus($userSubscription);

        $this->persist();

        $this->validateSuccess();
    }

    private function validate(UserSubscription $subscription): void
    {
        if (! $this->basicValidation()) {
            throw new ValidationException();
        }

        if (! $subscription->isActive()) {
            throw new ValidationException();
        }
    }

    private function updateSubscriptionStatus(UserSubscription $userSubscription)
    {
        $userSubscription->setStatus(UserSubscription::STATUS_CANCELLED);

        $this->em->persist($userSubscription);
    }

    private function validateSuccess()
    {
        if ($this->userSubscriptionNotCancelled()) {
            throw new ApplicationException('Failed to update user subscription');
        }
    }
}
