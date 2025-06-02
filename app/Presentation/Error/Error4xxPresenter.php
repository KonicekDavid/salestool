<?php

declare(strict_types=1);

namespace App\Presentation\Error;

use Nette;
use Nette\Application\Attributes\Requires;
use Nette\Application\Responses\JsonResponse;

/**
 * Handles 4xx HTTP error responses.
 */
#[Requires(methods: '*', forward: true)]
final class Error4xxPresenter extends Nette\Application\UI\Presenter
{
    public function renderDefault(Nette\Application\BadRequestException $exception): void
    {
        $response = new JsonResponse(['message' => 'Bad request.']);
        $this->getHttpResponse()->setCode($exception->getCode());
        $this->sendResponse($response);
    }
}
