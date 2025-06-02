<?php

declare(strict_types=1);

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

/**
 * CalculationPresenter
 */
final class CalculationPresenter extends Presenter
{
    /**
     * @var CalculationFacadeInterface
     */
    #[Inject]
    public CalculationFacadeInterface $calculationFacade;

    /**
     * @var CalculationRepositoryInterface
     */
    #[Inject]
    public CalculationRepositoryInterface $calculationRepository;

    /**
     * @return void
     */
    public function startup(): void
    {
        parent::startup();
    }

    /**
     * @param int|null $id
     * @return void
     */
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
                $page = $this->getParameter('page', 1);
                $page = max(1, is_numeric($page) ? (int)$page : 1);
                $limit = $this->getParameter('limit', CalculationRepository::LIMIT);
                $limit = min(
                    CalculationRepository::MAX_LIMIT,
                    is_numeric($limit) ? (int)$limit : CalculationRepository::LIMIT
                );
                $data = $this->calculationFacade->getList($page, $limit);
            } catch (\Throwable $ex) {
                Debugger::log($ex->getMessage(), Debugger::ERROR);
                $this->getHttpResponse()->setCode(Response::S500_InternalServerError);
                $this->terminate();
            }
            $this->sendJson($data);
        }
    }

    /**
     * @return void
     */
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

    /**
     * @param int $id
     * @return void
     */
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
