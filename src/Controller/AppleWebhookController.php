<?php

namespace App\Controller;

use App\Service\Actions\Subscription\SubscriptionAction;
use App\Service\Apple\Subscription;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;

class AppleWebhookController extends SubscriptionWebhook
{
    private Subscription $appleSubscriptionService;

    public function __construct(Subscription $appleSubscriptionService)
    {
        $this->appleSubscriptionService = $appleSubscriptionService;
    }

    /**
     * @Route("/webhooks/apple", methods={"POST"} name="apple_webhook")
     */
    public function index(Request $request): Response
    {
        return $this->handle($request);
    }

    protected function parseRequest(Request $request): array // should be SubscriptionRequest object
    {
        return $this->appleSubscriptionService->parseRequest($request);
    }

    protected function resolveAction(Request $request): SubscriptionAction
    {
        return $this->appleSubscriptionService->getSubscriptionAction($request->get('notification_type'));
    }

    protected function getSuccessResponse(): Response
    {
        return new Response(); // it's enough since only 200 status is required
    }

    protected function getFailureResponse(): Response
    {
        return new Response(null, 400);
    }

    protected function validateRequest(Request $request)
    {
        if ($this->getParameter('apple.subscription_secret') !== $request->get('password')) {
            throw new UnauthorizedHttpException(Response::HTTP_FORBIDDEN);
        }

        if ($this->getParameter('container.env') === 'prod' && $request->get('environment') !== 'PROD') {
            throw new UnauthorizedHttpException(Response::HTTP_FORBIDDEN);
        }
    }
}
