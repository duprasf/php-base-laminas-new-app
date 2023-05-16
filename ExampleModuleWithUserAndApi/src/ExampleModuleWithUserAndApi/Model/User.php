<?php
namespace ExampleModuleWithUserAndApi\Model;

use PDO;
use UserAuth\Module as UserAuth;
use UserAuth\Model\DbUser;
use UserAuth\Exception\InvalidCredentialsException;
use UserAuth\Exception\UserException;
use UserAuth\Exception\JwtException;
use UserAuth\Exception\JwtExpiredException;

class User extends DbUser
{
    /**
    * @var ID_FIELD, when extended, this is the name of the "unique" field that
    * represent the user
    */
    protected const ID_FIELD = 'email';

    /**
    * @var PDO
    * @internal
    */
    private $pdo;
    /**
    * Set the PDO connection to manage your users
    * This setter is used by the factory
    *
    * @param PDO $pdo
    * @return User
    */
    public function setDb(PDO $pdo)
    {
        $this->pdo = $pdo;
        return $this;
    }
    /**
    * Get the PDO connection
    *
    * @return PDO
    */
    protected function getDb()
    {
        return $this->pdo;
    }

    /**
    * Authenticate/login a user using a database. This particular implementation would use a central
    * DB for user and each app could have a user param, that's why it uses a parentDb for authenticating
    *
    * ***DEBUG ---> For this example without a real DB, the password is "test" without the quotes
    *
    * @param String $email
    * @param String $password
    * @return bool, true if successful false otherwise
    * @throws InvalidCredentialsException In this implementation, throw exception when credentials are incorrect
    * @throws UserException this is thrown when no "parentDb" is defined.
    */
    public function authenticate(String $email, String $password) : bool
    {
        // signal that the login process will start
        $this->getEventManager()->trigger(UserAuth::EVENT_LOGIN.'.pre', $this, ['email'=>$email]);

        //$pdo = $this->getDb();
        // prepare your query to get the correct user row from the DB
        //$prepared = $pdo->prepare("SELECT userId, password, status, emailVerified FROM `user` WHERE email LIKE ?");
        // get the user row using the pass param (could be userId, username, email, etc.)
        //$prepared->execute([$email]);
        //$data = $prepared->fetch(PDO::FETCH_ASSOC);
        // ***DEBUG ---> emulate the DB content (the password is "test" without the quotes)
        $data = ['email'=>$email, 'status'=>1, 'userId'=>1, 'password'=>'$2y$10$yJXNxVITwzgAFhfzwqWJKOySRjF1wueVX81K6/eTcitbmIZ4Qvx3y'];

        // if there is no data/user or if the password does not match...
        if(!$data || !password_verify($password, $data['password'])) {
            // ... then signal that the login failed
            // In the event, when userId is null it means that the email was not found at all,
            // if it is set, the password was wrong
            $this->getEventManager()->trigger(UserAuth::EVENT_LOGIN_FAILED, $this, ['email'=>$email, 'userId'=>$data['userId']??null]);

            // can return false or throw an exception, it depends on your implementation
            throw new InvalidCredentialsException();
        }

        // remove the password from the data array for security (it is an hash but still, better safe than sorry)
        unset($data['password']);

        $this->exchangeArray($data);
        // save user data in session if config allows
        // It is much safer to pass the JWT to all request instead of keeping a session
        // but I know not every use case would work with that.
        $this->buildLoginSession($data);

        // signal that the login was successful
        $this->getEventManager()->trigger(UserAuth::EVENT_LOGIN, $this, ['email'=>$email, 'id'=>$this[self::ID_FIELD] ?? null]);
        return true;
    }

    /**
    * A method used by loadFromJwt and loadFromSession to load the user without validating credentials
    *
    * This function would load from the DB, but in this example, we simulate the DB
    * so we only return some fake data
    *
    * @param int $id
    * @return bool
    */
    protected function _loadUserById(int $id) : bool
    {
        // this function would load from the DB, but in this example, we simulate the DB
        // so we only return some fake data
        $data = [
            'userId'=>$id,
            'email'=>'fake-test@hc-sc.gc.ca',
            'status'=>1,
        ];
        $this->exchangeArray($data);
        $this->buildLoginSession($data);
        return true;
    }

    /**
    * Load a user from the JWT
    *
    * @param String $jwt the JavaScript Web Token received from the client
    * @return bool, true if successful false otherwise
    * @throws JwtException If the token is null or invalid
    * @throws JwtExpiredException If the token is expired
    * @throws UserException if the ID field is not set in the JWT
    */
    public function loadFromJwt(?String $jwt) : bool
    {
        if($jwt == null) {
            throw new JwtException('JWT is null');
        }
        $data = $this->jwtToData($jwt);
        if(!isset($data[self::ID_FIELD])) {
            throw new UserException('ID field ('.self::ID_FIELD.') does not exists in JWT');
        }
        // since this is only an example, we just return the data from the JWT
        $this->exchangeArray($data);
        return true;
    }


    /**
    * This function is called when trying to get the JWT
    *
    * @param int $time, the length of time the JWT will be valid. It should not change anything, but just in case...
    * @return array, the data you want to send to client as part of the JWT
    */
    protected function getDataForJWT($time) : array
    {
        $payload = $this->getArrayCopy();
        if(!isset($payload['id'])) {
            $payload['id'] = $this[self::ID_FIELD] ?? null;
        }
        // here you can add or remove any fields you want to send to the client
        $payload['debug']='debug string';
        $payload['type']='db';

        // you must return an array, even an empty array would work, but would be completely useless
        return $payload;
    }
}
