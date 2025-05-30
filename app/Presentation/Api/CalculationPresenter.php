<?php

declare(strict_types=1);
/**
 * @author David Koníček
 */

namespace App\Presentation\Api;

use App\Model\Calculation\CalculationFacadeInterface;
use App\Model\Calculation\CalculationRepository;
use App\Model\Calculation\CalculationRepositoryInterface;
use Nette\Application\AbortException;
use Nette\Application\Attributes\Requires;
use Nette\Application\UI\Presenter;
use Nette\DI\Attributes\Inject;
use Nette\Http\Response;
use Tracy\Debugger;

class CalculationPresenter extends Presenter
{
    #[Inject]
    public CalculationFacadeInterface $calculationFacade;

    #[Inject]
    public CalculationRepositoryInterface $calculationRepository;

    /**
     * @param int|null $id
     * @return void
     * @throws AbortException
     */
    #[Requires(methods: ['GET', 'POST', 'PUT'], forward: false, actions: 'default')]
    public function actionDefault(?int $id = null): void
    {
        $response = $this->getHttpResponse();

        try {
            switch ($this->getHttpRequest()->getMethod()) {
                case 'POST':
                    $data = json_decode($this->getHttpRequest()->getRawBody() ?? '', true) ?? [];
                    $calculationId = $this->calculationFacade->create($data);
                    $response->setCode(Response::S201_Created);
                    break;
                case 'PUT':
                    $data = json_decode($this->getHttpRequest()->getRawBody() ?? '', true) ?? [];
                    if ($id !== null) {
                        $calculation = $this->calculationRepository->getById($id);
                        if (!$calculation) {
                            $this->error('Calculation not found');
                        }
                        $calculation = $this->calculationFacade->update($calculation, $data);
                        $response->setCode(Response::S200_OK);
                    } else {
                        $response->setCode(Response::S400_BadRequest, 'Specified id is missing in url.');
                    }
                    break;
                case 'GET':
                    if ($id !== null) {
                        $calculation = $this->calculationRepository->getById($id);
                        if (!$calculation) {
                            $this->error('Calculation not found');
                        }
                        $response->setCode(Response::S200_OK);
                    } else {
                        $limit = $this->getRequest()?->getParameter('limit');
                        $offset = $this->getRequest()?->getParameter('offset');
                        $limit = is_int($limit) ? $limit : CalculationRepository::LIMIT;
                        $offset = is_int($offset) ? $offset : CalculationRepository::OFFSET;
                        $data = $this->calculationFacade->getList($limit, $offset);
                    }
                    break;
                default:
            }
        } catch (\InvalidArgumentException $e) {
            Debugger::log($e->getMessage(), Debugger::ERROR);
            $response->setCode(Response::S400_BadRequest, $e->getMessage());
        } catch (\Throwable $ex) {
            Debugger::log($ex->getMessage(), Debugger::ERROR);
            $response->setCode(Response::S500_InternalServerError);
        }
        $this->sendJson($data);
//        $this->terminate();
    }
}