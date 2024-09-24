<?php

declare(strict_types=1);

namespace ExampleModule\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Application\Model\Breadcrumbs;
use GcNotify\GcNotify;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $view = $this->exampleModuleCommonMetadata(new ViewModel());
        $view->setVariable('id', $this->params()->fromRoute('id', 1));
        return $view;
    }

    public function javascriptAction()
    {
        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/javascript');
        $response->getHeaders()->addHeaderLine('Content-Language', 'en');

        $view = new ViewModel();
        $view->setTerminal(true);
        return $view;
    }

    public function splashAction()
    {
        $view = $this->exampleModuleCommonMetadata(new ViewModel());
        $view->setVariable('homeRoute', 'ExampleModule/first-page');
        $view->setTemplate('layout/splash');
        return $view;
    }
}
