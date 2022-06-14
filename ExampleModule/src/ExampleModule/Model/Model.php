<?php
namespace ExampleModule\Model;

class Model
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

    /**
    * an example of a method to be called
    *
    * @param array $data
    */
    public function doSomething(array $data)
    {
        $pdo = $this->getDb();
        if($pdo) {
            $pdo->beginTransaction();
            $prepared = $pdo->prepare("INSERT INTO yourTable SET name=:name, message=:message");
            //$prepared->execute([$data['name'], $data['message']]);
        }
    }
}
