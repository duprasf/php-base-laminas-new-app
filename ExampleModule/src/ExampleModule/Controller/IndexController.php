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
