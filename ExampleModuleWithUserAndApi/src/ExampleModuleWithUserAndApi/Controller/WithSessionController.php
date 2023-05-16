<?php
declare(strict_types=1);

namespace ExampleModuleWithUserAndApi\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Application\Model\Breadcrumbs;
use GcNotify\GcNotify;
use ExampleModuleWithUserAndApi\Model\User;

class WithSessionController extends AbstractActionController
{
    private $user;
    public function setUser(User $user) : self
    {
        $this->user = $user;
        return $this;
    }
    protected function getUser() : User
    {
        return $this->user;
    }

    public function indexAction()
    {
        $view = $this->_setCommonMetadata(new ViewModel());
        $user = $this->getUser();

        $entries = [];
        if($user->isLoggedIn()) {
            $entries = [
                ['name'=>'first item', 'category'=>'green'],
                ['name'=>'second item', 'category'=>'green'],
                ['name'=>'third item', 'category'=>'red'],
                ['name'=>'item 4', 'category'=>'green'],
                ['name'=>'item 5', 'category'=>'green'],
                ['name'=>'item 6', 'category'=>'gold'],
                ['name'=>'item 7', 'category'=>'green'],
                ['name'=>'item 8', 'category'=>'green'],
            ];
        }

        $view->setVariable('content', $entries);
        $view->setVariable('user', $user);

        return $view;
    }

    /**
    * Set the common metadata for this project
    *
    * @param ViewModel $view
    *
    * @return ViewModel
    */
    public function _setCommonMetadata(ViewModel $view)
    {
        $translator = $this->getTranslator();
        $lang = $translator->getLang();
        $view->setVariable('metadata', new \ArrayObject(array(
            "title" => $translator->translate('ExampleModuleWithUserAndApi'),
            "description"=>$translator->translate("ExampleModuleWithUserAndApi"),
            "issuedDate"=>date('Y-m-d'),
            //"extra-css"=>'/css/stylesheet.css',
            //"extra-js"=>'/js/script.js',
        )));

        $view->setVariable('attribution', 'HC');

        $breadcrumbItems = new Breadcrumbs();
        if($lang == 'fr') {
            $breadcrumbItems->addBreadcrumbs([
                'http://canada.ca/'.$lang => 'Canada.ca',
                // put the default breadcrumbs for your app here (in French)
            ]);
        } else {
            $breadcrumbItems->addBreadcrumbs([
                'http://canada.ca/'.$lang => 'Canada.ca',
                // put the default breadcrumbs for your app here (in English)
            ]);
        }
        $view->setVariable('breadcrumbItems', $breadcrumbItems);
        $view->setVariable('metadata', new \ArrayObject());

        return $view;
    }
}
