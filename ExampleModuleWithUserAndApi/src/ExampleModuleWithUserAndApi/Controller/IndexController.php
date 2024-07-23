<?php

declare(strict_types=1);

namespace ExampleModuleWithUserAndApi\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Application\Model\Breadcrumbs;
use GcNotify\GcNotify;
use Laminas\Session\Container;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $view = $this->exampleModuleWithUserAndApiCommonMetadata(new ViewModel());
        $view->setVariable('token', $this->params()->fromRoute('token'));
        return $view;
    }

    public function returnAction()
    {
        $view = new ViewModel();
        $view->setTerminal(true);
        $view->setVariable('token', $this->params()->fromRoute('token'));

        return $view;
    }
}
