<?php

namespace App\Service\Actions\Subscription;

use App\Entity\UserSubscription;
use App\Exceptions\ApplicationException;
use App\Exceptions\ValidationException;
use App\Service\Actions\Action;

class Renew extends SubscriptionAction implements Action
{
    public function handle(): void
    {
        $user = $this->getUser();
        $subscription = $this->getSubscription();
        $userSubscription = $this->getUserSubscription($user, $subscription);

        $this->validate($userSubscription);

        $this->renewUserSubscription($userSubscription);
        $this->addTransaction($user, $subscription);

        $this->persist();

        $this->validateSuccess();
    }

    private function validate(UserSubscription $subscription): void
    {
        if (! $this->basicValidation()) {
            throw new ValidationException();
        }

        if (! $subscription->isRenewable()) {
            throw new ValidationException();
        }

        if (! $this->validateAmount($subscription)) {
            throw new ValidationException();
        }
    }

    private function renewUserSubscription(UserSubscription $userSubscription)
    {
        $userSubscription->setStatus(UserSubscription::STATUS_ACTIVE)
            ->setActiveTo($this->activeTo);

        $this->em->persist($userSubscription);
    }

    private function validateSuccess()
    {
        if ($this->userSubscriptionNotRenewed()) {
            throw new ApplicationException('Failed to renew user subscription');
        }
    }
}
