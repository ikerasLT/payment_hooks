<?php

namespace App\Controller;

use App\Exceptions\ApplicationException;
use App\Exceptions\ValidationException;
use App\Service\Actions\Subscription\SubscriptionAction;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class SubscriptionWebhook extends AbstractController
{
    abstract protected function validateRequest(Request $request);

    abstract protected function parseRequest(Request $request): array; // should be SubscriptionRequest object

    abstract protected function resolveAction(Request $request): SubscriptionAction;

    abstract protected function getSuccessResponse(): Response;

    abstract protected function getFailureResponse(): Response;

    protected function handle(Request $request): Response
    {
        $this->validateRequest($request);
        $data = $this->parseRequest($request);
        $success = $this->doAction($request, $data);

        if ($success) {
            return $this->getSuccessResponse();
        }

        return $this->getFailureResponse();
    }

    protected function doAction(Request $request, $data): bool
    {
        $action = $this->resolveAction($request);

        try {
            $action->setData($data)
                ->handle();
        } catch (ValidationException | ApplicationException $e) {
//            todo: log
            return false;
        }

        return true;
    }
}
