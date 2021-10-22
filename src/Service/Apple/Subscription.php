<?php

namespace App\Service\Apple;

use App\Service\Actions\Subscription\Cancel;
use App\Service\Actions\Subscription\Create;
use App\Service\Actions\Subscription\FailToRenew;
use App\Service\Actions\Subscription\Renew;
use App\Service\Actions\Subscription\SubscriptionAction;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;

// TODO: refactor into separate services RequstParser, ActionMapper etc
class Subscription
{
    private KernelInterface $container;

    private $actionMap = [
        'INITIAL_BUY'       => Create::class,
        'DID_RENEW'         => Renew::class,
        'DID_FAIL_TO_RENEW' => FailToRenew::class,
        'CANCEL'            => Cancel::class,
    ];

    public function __construct(KernelInterface $container)
    {
        $this->container = $container;
    }

    public function parseRequest(Request $request): array // should be SubscriptionRequest object
    {
        $receipt = $request->get('unified_receipt');
        $receipt_info = data_get($receipt, 'latest_receipt.0');

        $subscriptionId = $request->get('auto_renew_product_id');
        $userId = $request->get('auto_renew_adam_id');
        $activeFromMs = data_get($receipt_info, 'original_purchase_date_ms');
        $activeFrom = new DateTime("@$activeFromMs");
        $activeToMs = data_get($receipt_info, 'expires_date_ms');
        $activeTo = new DateTime("@$activeToMs");
        $externalId = data_get($receipt_info, 'transaction_id');
        $provider = 'Apple';
        $quantity = data_get($receipt_info, 'quantity');
        $purchasedAt = $activeFrom;

        return compact(
            'subscriptionId',
            'userId',
            'activeFrom',
            'activeTo',
            'externalId',
            'provider',
            'quantity',
            'purchasedAt'
        );
    }

    public function getSubscriptionAction($type): SubscriptionAction
    {
        $actionClass = data_get($this->actionMap, $type);

        if (! $actionClass) {
            throw new \InvalidArgumentException('Unsupported Action');
        }

        return $this->container->getContainer()->get($actionClass);
    }
}
