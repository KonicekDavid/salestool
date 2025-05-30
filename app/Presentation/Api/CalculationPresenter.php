<?php

declare(strict_types=1);
/**
 * @author David Koníček
 */

namespace App\Presentation\Api;

use App\Model\Calculation\CalculationDataValidator;
use App\Model\Calculation\CalculationFacadeInterface;
use App\Model\Calculation\CalculationRepository;
use App\Model\Calculation\CalculationRepositoryInterface;
use Nette\Application\Attributes\Requires;
use Nette\Application\BadRequestException;
use Nette\Application\Responses\JsonResponse;
use Nette\Application\UI\Presenter;
use Nette\DI\Attributes\Inject;
use Nette\Http\IResponse;
use Nette\Http\Response;
use Tracy\Debugger;

class CalculationPresenter extends Presenter
{
    #[Inject]
    public CalculationFacadeInterface $calculationFacade;

    #[Inject]
    public CalculationRepositoryInterface $calculationRepository;

    public function startup(): void
    {
        parent::startup();
    }

    #[Requires(methods: 'GET', forward: false, actions: 'read')]
    public function actionRead(?int $id = null): void
    {
        if ($this->getHttpRequest()->getMethod() !== 'GET') {
            $this->error('Invalid request method', IResponse::S400_BadRequest);
        }

        if ($id !== null) {
            $calculation = $this->calculationRepository->getById($id);
            if (!$calculation) {
                $this->error('Calculation not found');
            }
            $this->sendJson($calculation->toArray());
        } else {
            $data = [];
            try {
                $limit = $this->getRequest()?->getParameter('limit');
                $offset = $this->getRequest()?->getParameter('offset');
                $limit = is_int($limit) ? $limit : CalculationRepository::LIMIT;
                $offset = is_int($offset) ? $offset : CalculationRepository::OFFSET;
                $data = $this->calculationFacade->getList($limit, $offset);
            } catch (\Throwable $ex) {
                Debugger::log($ex->getMessage(), Debugger::ERROR);
                $this->getHttpResponse()->setCode(Response::S500_InternalServerError);
                $this->terminate();
            }
            $this->sendJson($data);
        }
    }

    #[Requires(methods: 'POST', forward: false, actions: 'create')]
    public function actionCreate(): void
    {
        if ($this->getHttpRequest()->getMethod() !== 'POST') {
            $this->error('Invalid request method', IResponse::S400_BadRequest);
        }

        $calculation = null;
        try {
            $json = $this->getHttpRequest()->getRawBody() ?? '';
            $calculationSchema = (new CalculationDataValidator())->validate($json);
            $calculation = $this->calculationFacade->create($calculationSchema);
        } catch (\InvalidArgumentException $ex) {
            Debugger::log($ex->getMessage(), Debugger::ERROR);
            $this->getHttpResponse()->setCode(Response::S400_BadRequest, $ex->getMessage());
            $this->terminate();
        } catch (\Throwable $ex) {
            Debugger::log($ex->getMessage(), Debugger::ERROR);
            $this->getHttpResponse()->setCode(Response::S500_InternalServerError);
            $this->terminate();
        }
        if ($calculation) {
            $this->getHttpResponse()->setCode(Response::S201_Created);
            $this->sendResponse(new JsonResponse($calculation->toArray()));
        }
        $this->error('Create Calculation failed', IResponse::S400_BadRequest);
    }

    #[Requires(methods: 'PUT', forward: false, actions: 'update')]
    public function actionUpdate(int $id): void
    {
        if ($this->getHttpRequest()->getMethod() !== 'PUT') {
            $this->error('Invalid request method', IResponse::S400_BadRequest);
        }

        try {
            $json = $this->getHttpRequest()->getRawBody() ?? '';
            $calculationSchema = (new CalculationDataValidator())->validate($json);
            $calculation = $this->calculationRepository->getById($id);
            if (!$calculation) {
                throw new BadRequestException('Calculation not found');
            }
            $updatedCalculation = $this->calculationFacade->update($calculation, $calculationSchema);
        } catch (\InvalidArgumentException $ex) {
            Debugger::log($ex->getMessage(), Debugger::ERROR);
            $this->getHttpResponse()->setCode(Response::S400_BadRequest, $ex->getMessage());
            $this->terminate();
        } catch (\Throwable $ex) {
            Debugger::log($ex->getMessage(), Debugger::ERROR);
            $this->getHttpResponse()->setCode(Response::S500_InternalServerError);
            $this->terminate();
        }

        if ($updatedCalculation) {
            $this->sendJson($updatedCalculation->toArray());
        } else {
            $this->getHttpResponse()->setCode(Response::S500_InternalServerError);
            $this->terminate();
        }
    }
}