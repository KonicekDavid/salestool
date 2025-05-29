<?php
declare(strict_types=1);
/**
 * @author David Koníček
 */

namespace App\ApiModule;

use Nette\Application\UI\Presenter;

class OfferPresenter extends Presenter
{
    public function actionDefault(): void
    {
        $this->sendJson(['OK']);
    }
}