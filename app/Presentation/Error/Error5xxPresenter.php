<?php

declare(strict_types=1);

namespace App\Presentation\Error;

use Nette;
use Nette\Application\Attributes\Requires;
use Nette\Application\Responses;
use Nette\Http;
use Tracy\ILogger;

/**
 * Handles uncaught exceptions and errors, and logs them.
 */
#[Requires(forward: true)]
final class Error5xxPresenter implements Nette\Application\IPresenter
{
    public function __construct(
        private ILogger $logger,
    ) {
    }


    public function run(Nette\Application\Request $request): Nette\Application\Response
    {
        // Log the exception
        $exception = $request->getParameter('exception');
        $this->logger->log($exception, ILogger::EXCEPTION);

        // Display a generic error message to the user
        return new Responses\CallbackResponse(
            function (Http\IRequest $httpRequest, Http\IResponse $httpResponse): void {
                header('Content-Type: application/json');
                echo json_encode(['message' => 'Aplication error.']);
            }
        );
    }
}
