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
        if($pdo && isset($data['name']) && isset($data['message'])) {
            try {
                $pdo->beginTransaction();
                $prepared = $pdo->prepare("INSERT INTO yourTable SET name=?, message=?");
                $prepared->execute([$data['name'], $data['message']]);
                $pdo->commit();
            } catch(\Exception $e) {
                $pdo->rollBack();
            }
        }
    }
}
