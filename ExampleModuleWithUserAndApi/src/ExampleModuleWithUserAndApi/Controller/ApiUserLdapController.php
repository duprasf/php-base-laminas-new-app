<?php
declare(strict_types=1);

namespace ExampleModuleWithUserAndApi\Controller;

use Laminas\Mvc\Controller\AbstractRestfulController;
use Laminas\View\Model\JsonModel;
use UserAuth\Model\UserInterface;
use UserAuth\Exception\UserException;
use UserAuth\Exception\InvalidCredentialsException;

class ApiUserLdapController extends AbstractRestfulController
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

    /**
    * This is required for APIs
    * The plugin setResponseHeaders will return which domain can call this API and using which verb
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
        $this->response->setStatusCode(405);

        return [
            'content' => 'Method Not Allowed'
        ];
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
        $user = $this->getUser();
        $type = $this->params()->fromRoute('type', 'login');

        try {
            if($type == 'register') {
                if(!$user->register($data['email'], $data['password'])) {
                    $this->response->setStatusCode(400);
                    return new JsonModel(['error' => 'Bad Request']);
                }
                $this->response->setStatusCode(201);
                return $this->returnUserData($user);
            }

            $user->login($data['email'], $data['password']);
            // if credential is wrong then a exception would be thrown, so no need for
            // a if() statement, if we pass the ->login() we are correctly logged in
            // you can do any other operation here before continuing

            return $this->returnUserData($user, remember:!!$data['remember']);

        } catch(InvalidCredentialsException $e) {
            $this->response->setStatusCode(401);
            return new JsonModel(['error' => 'Invalid credentials']);
        } catch(UserException $e) {
            $this->response->setStatusCode(500);
            return new JsonModel(['error'=>'Unknown error, please try again']);
        } catch(\Exception $e) {
            $this->response->setStatusCode(500);
            return new JsonModel(['error'=>'Unknown error, please try again']);
        }

        $this->response->setStatusCode(500);
        return new JsonModel(['error'=>'Unknown error, please try again']);
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
