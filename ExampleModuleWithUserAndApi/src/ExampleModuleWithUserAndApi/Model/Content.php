<?php
namespace ExampleModuleWithUserAndApi\Model;

use UserAuth\Model\UserInterface;

class Content
{
    /**
    * This setter is used by the factory
    * The getter is used within this classe
    */
    private $pdo;
    public function setDb(\PDO $pdo)
    {
        $this->pdo = $pdo;
        return $this;
    }
    protected function getDb()
    {
        return $this->pdo;
    }

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

    public function getContent()
    {
        // Normally, you would pull data from a database or another source like this...
        /*
        $pdo = $this->getDb();
        $query="SELECT * FROM content WHERE owner=?";

        $prepared=$pdo->prepare($query);

        $prepared->execute([
            $this->getUser()->userId
        ]);
        return $prepared->fetchAll(\PDO::FETCH_ASSOC);
        /**/


        // ... but for this example, we will just return some fake data
        $entries = [
            ['name'=>'first item', 'category'=>'green'],
            ['name'=>'second item', 'category'=>'green'],
            ['name'=>'third item', 'category'=>'red'],
        ];
        if($this->getUser()->isLoggedIn()) {
            $entries[] = ['name'=>'item 4', 'category'=>'green'];
            $entries[] = ['name'=>'item 5', 'category'=>'gold'];
            $entries[] = ['name'=>'item 6', 'category'=>'green'];
            $entries[] = ['name'=>'item 7', 'category'=>'green'];
            $entries[] = ['name'=>'item 8', 'category'=>'green'];
        }
        return $entries;
    }
}
