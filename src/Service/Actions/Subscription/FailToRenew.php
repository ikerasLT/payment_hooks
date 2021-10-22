<?php

namespace App\Service\Actions\Subscription;

use App\Entity\UserSubscription;
use App\Exceptions\ApplicationException;
use App\Exceptions\ValidationException;
use App\Service\Actions\Action;

class FailToRenew extends SubscriptionAction implements Action
{
    public function handle(): void
    {
        $user = $this->getUser();
        $subscription = $this->getSubscription();
        $userSubscription = $this->getUserSubscription($user, $subscription);

        $this->validate($userSubscription);

        $this->updateSubscriptionStatus($userSubscription);
        $this->addTransaction($user, $subscription, false);

        $this->persist();

        $this->validateSuccess();
    }

    private function validate(UserSubscription $subscription): void
    {
        if (! $this->basicValidation()) {
            throw new ValidationException();
        }

        if (! $subscription->shouldBeRenewed()) {
            throw new ValidationException();
        }
    }

    private function updateSubscriptionStatus(UserSubscription $userSubscription)
    {
        $userSubscription->setStatus(UserSubscription::STATUS_FAILED);

        $this->em->persist($userSubscription);
    }

    private function validateSuccess()
    {
        if ($this->userSubscriptionNotFailed()) {
            throw new ApplicationException('Failed to update user subscription');
        }
    }
}
