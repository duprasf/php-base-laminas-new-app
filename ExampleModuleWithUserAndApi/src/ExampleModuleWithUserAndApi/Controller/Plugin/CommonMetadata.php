<?php

namespace ExampleModuleWithUserAndApi\Controller\Plugin;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Laminas\View\Model\ViewModel;
use Laminas\Mvc\I18n\Translator as MvcTranslator;
use Application\Model\Breadcrumbs;
use Application\Controller\Plugin\CommonMetadata as ParentCommonMetadata;
use Application\Factory\Controller\Plugin\CommonMetadataFactory;

class CommonMetadata extends ParentCommonMetadata
{
    public function __invoke(ViewModel $view)
    {
        $translator = $this->getTranslator();
        $lang = $this->getLang();
        $url = $this->getUrlObj();
        $array = [
            "title" => $translator->translate('Example Module With User And Api'),
            "appName" => $translator->translate('ExampleModuleWithUserAndApi'),
            "description" => $translator->translate("ExampleModuleWithUserAndApi"),
            "versionNumber" => '1.0',
            "isApp" => true,
            "contactLinks" => getenv('ADMIN_EMAIL') ? ["mailto:".getenv('ADMIN_EMAIL')] : [],
            "showShare" => false,
            "showFeedback" => false,
            "appUrl" => $url('ExampleModuleWithUserAndApi/first-page'),
            "extra-css" => [
                //'/example-module/css/example-module.css'
            ],
            "extra-js" => [
                //'/example-module/js/example-module.min.js',
            ],
        ];
        /*
        // If you have a user and need to set the signIn, signOut button,
        // set the links here.
        if($this->getUser()->isLoggedIn()) {
            $array["signOutUrl"]=$url('locale/directory/logout');
        } else {
            $array["signInUrl"]=$url('locale/directory/login');
        }
        /**/

        $view->setVariable('metadata', $this->getMetadataObj()->merge($array));
        $view->setVariable('attribution', 'HC');

        $breadcrumbs = $this->getBreadcrumbsObj();
        $breadcrumbItems = [
            'http://canada.ca/'.$lang => 'Canada.ca',
            // put the default breadcrumbs for your app here (in French)
            $this->getUrlObj()('ExampleModuleWithUserAndApi/first-page') => $translator->translate('Example Module'),
        ];
        $breadcrumbs($breadcrumbItems);
        $view->setVariable('breadcrumbItems', $breadcrumbs);

        $route = $this->getRouteMatch();

        $routeName = $route->getMatchedRouteName();
        $routeParams = $route->getParams();
        $url = $this->getUrlObj();

        $otherLang = $this->getLang() == 'en' ? 'fr' : 'en';
        $params = $routeParams;
        $params['locale'] = $otherLang;
        $params['lang'] = $otherLang;
        $view->setVariable(
            'switch-lang-url',
            $url(
                $routeName,
                $params,
                array('locale' => $otherLang, 'translator' => $translator)
            )
        );

        return $view;
    }
}
