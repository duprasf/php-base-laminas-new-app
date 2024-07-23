<?php

declare(strict_types=1);

namespace ExampleModuleWithUserAndApi\Controller;

use Laminas\Mvc\Controller\AbstractRestfulController;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;
use UserAuth\Model\User\UserInterface;
use UserAuth\Exception\JwtException;
use UserAuth\Exception\JwtExpiredException;
use ExampleModuleWithUserAndApi\Model\Content;

// first a restfulController should not be only "api" but should
// be a specific controller like "ApiMembersController" or
// "ApiTransactionController"
class ApiContentController extends AbstractRestfulController
{
    private $userObj;
    /**
    * Set the User obj implementing UserInterface (used in factory)
    *
    * @param UserInterface $obj
    * @return ApiUserController
    */
    public function setUser(UserInterface $obj)
    {
        $this->userObj = $obj;
        return $this;
    }
    protected function getUser()
    {
        return $this->userObj;
    }

    private $contentObj;
    /**
    * Set the User obj implementing UserInterface (used in factory)
    *
    * @param UserInterface $obj
    * @return ApiUserController
    */
    public function setContentObj(Content $obj)
    {
        $this->contentObj = $obj;
        return $this;
    }
    protected function getContentObj()
    {
        return $this->contentObj;
    }

    /**
    * This is required for APIs
    * The plugin setResponseHeaders will return which domain can call
    * this API and which verb are accepted
    *
    */
    public function options()
    {
        return $this->setResponseHeaders($this->getResponse());
    }

    /**
    * GET maps to either get() or getList(), depending on whether or not an "id"
    * parameter is found in the route matches. If one is, it is passed as an
    * argument to get(); if not, getList() is invoked. In the former case, you
    * should provide a representation of the given entity with that identification;
    * in the latter, you should provide a list of entities.
    *
    * @param mixed $id
    */
    public function get($id)
    {
        $this->response->setStatusCode(405);

        return [
            'content' => 'Method Not Allowed'
        ];
    }
    public function getList()
    {
        $response = $this->setResponseHeaders($this->getResponse());
        $view = new JsonModel();

        try {
            // API should always return a JWT as authentication (see
            // ApiUserController) unless your method is completely open
            // which is also acceptable for some operations
            // In this case, I set the X-Access-Token as the JWT
            $jwt = $this->params()->fromHeader('X-Access-Token');
            // ->fromHeader() returns an object, not the direct value
            $jwt = $jwt ? $jwt->getFieldValue() : null;
            if($jwt === "null") {
                $jwt = null;
            }

            $user = $this->getUser();
            // If your API can return data without a valid user you can
            // keep this try{}catch block, if you don't serve anything anonymously
            // just keep the $user->loadFromJwt($jwt); line
            try {
                $user->loadFromJwt($jwt);
            } catch (\Exception $e) {
            }
            $view->setVariable('data', $this->getContentObj()->getContent());

            return $view;
        } catch (JwtExpiredException $e) {
            $this->response->setStatusCode(401);
            $view->setVariables(['error' => "Your session is expired. Please login again.", 'code' => 408]);
        } catch (JwtException $e) {
            $this->response->setStatusCode(401);
            $view->setVariables(['error' => "Invalid or missing authentication session."]);
        } catch (InvalidUserException $e) {
            $this->response->setStatusCode(403);
            $view->setVariables(['error' => "Is this a hacking attemp?"]);
        } catch (\Exception $e) {
            $this->response->setStatusCode(500);
            $view->setVariables(['error' => 'Unknown error']);
        }
        return $view;
    }

    /**
    * POST maps to create(). That method expects a $data argument, usually the
    * $_POST superglobal array. The data should be used to create a new entity,
    * and the response should typically be an HTTP 201 response with the
    * Location header indicating the URI of the newly created entity and
    * the response body providing the representation.
    *
    * @param array $data
    */
    public function create($data)
    {
        $response = $this->setResponseHeaders($this->getResponse());
        $this->response->setStatusCode(405);

        return [
            'content' => 'Method Not Allowed'
        ];
    }

    /**
    * PUT maps to update(), and requires that an "id" parameter exists in the
    * route matches; that value is passed as an argument to the method. It
    * should attempt to update the given entity, and, if successful, return
    * either a 200 or 202 response status, as well as the representation of
    * the entity.
    *
    * @param mixed $id
    */
    public function update($id, $data)
    {
        $this->response->setStatusCode(405);

        return [
            'content' => 'Method Not Allowed'
        ];
    }

    /**
    * DELETE maps to delete(), and requires that an "id" parameter exists in
    * the route matches; that value is passed as an argument to the method. It
    * should attempt to delete the given entity, and, if successful, return
    * either a 200 or 204 response status.
    *
    * @param mixed $id
    */
    public function delete($id)
    {
        $this->response->setStatusCode(405);

        return [
            'content' => 'Method Not Allowed'
        ];
    }
}
