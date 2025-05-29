<?php

declare(strict_types=1);
/**
 * @author David Koníček
 */

namespace App\Presentation\Api;

use App\Model\CalculationFacadeInterface;
use App\Model\CalculationRepositoryInterface;
use Nette\Application\AbortException;
use Nette\Application\Attributes\Requires;
use Nette\Application\UI\Presenter;

class CalculationPresenter extends Presenter
{
    /**
     * @var CalculationFacadeInterface $calculationFacade @inject
     */
    public CalculationFacadeInterface $calculationFacade;

    /** @var CalculationRepositoryInterface $calculationRepository @inject */
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
            switch ($this->getRequest()?->getMethod()) {
                case 'GET':
                    $limit = $this->getRequest()?->getParameter('limit');
                    $offset = $this->getRequest()?->getParameter('offset');
                    $limit = is_int($limit) ? $limit : 10;
                    $offset = is_int($offset) ? $offset : 0;

                    $data = $this->calculationFacade->getList($limit, $offset);
                    break;
                case 'POST':
                    break;
                case 'PUT':
                    break;
                default:
            }
        } catch (\Throwable $ex) {
        }
        $this->terminate();
    }
}