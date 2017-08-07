<?php
namespace bookkeeping\Model;
use \DateTime;
use \PDO;

class User
{
    const TABLE_NAME = 'user';
    private static $id;
    public $token;
    public $email;
    public $given_name;
    public $family_name;
    public $picture;
    public $link;
    public $gender;

    public $error_validation;

    function __construct()
    {
    }

    /**
     * @param $id
     * @return Obj
     */
    static function getById($id)
    {
        if(!$id)
        {
            return false;
        }

        $DB = DB::getInstance();
        $sql = "SELECT * FROM `" . self::TABLE_NAME . "` WHERE  id = " . $id;
        $answer = $DB->query($sql);
        return $answer;
    }

    /**
     * @return array|bool
     */
    function getByEmail($email)
    {
        $DB = DB::getInstance();
        $sql = "SELECT * FROM `" . self::TABLE_NAME . "` WHERE  email = '" . $email ."'";
        $answer = $DB->query($sql);

        if(! empty($answer))
        {
            return $answer;
        } else {
            return false;
        }
    }

    public static function getId()
    {
        return self::$id;
    }


    /**
     * Check Auth user
     * @return bool
     */
    public static function checkAuth( )
    {
        if(isset($_SESSION['user_id']) && isset($_SESSION['token']))
        {
            return self::checkToken($_SESSION['user_id'], $_SESSION['token']);
        } else {
            return false;
        }
    }

    /**
     * Check $_Session token with saved token in DB
     * @param $user_id
     * @param $token
     * @return bool
     */
    public function checkToken($user_id, $token)
    {
        $DB = DB::getInstance();
        $sql = "SELECT * FROM `" . self::TABLE_NAME . "` WHERE  id = " . $user_id . " LIMIT 1";
        $user = $DB->query($sql)[0];

        // Проверка существования записи совпадения token
        if(! isset($user) || $user->token != $token)
        {
            //session_destroy();
            return false;
        }

        self::$id = $user_id;
        return true;

    }

    /**
     * Update token in DB and $_Session
     * @return bool
     */
    public function updateToken()
    {

        $DB = DB::getInstance();
        $token = bin2hex(random_bytes('64'));

        $sql = "UPDATE `" . self::TABLE_NAME . "` SET
                token = :token
                WHERE id = :id
                ";

        $params = [
            ':token' => $token,
            ':id' => self::$id
        ];

        $result = $DB->execute($sql, $params);

        if($result)
        {
            return $token;
        } else {
            return false;
        }
    }

    function setSession($user_id)
    {
        $token = bin2hex(random_bytes('64'));
        $_SESSION['token'] =  $token;
        $_SESSION['user_id'] = $user_id;

        $sql = "UPDATE `" . self::TABLE_NAME . "` SET
                token = :token
                WHERE id = :id
                ";

        $params = [
            ':token' => $token,
            ':id' => $user_id
        ];

        $DB = DB::getInstance();
        $result = $DB->execute($sql, $params);

        if($result)
        {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    function save()
    {
        // Create
        $sql = "INSERT INTO `" . self::TABLE_NAME . "`
                    (email,
                    given_name,
                    family_name,
                    picture,
                    link,
                    gender)
                     VALUES (
                    :email,
                    :given_name,
                    :family_name,
                    :picture,
                    :link,
                    :gender
                    )";

        $params = [
            ':email' => $this->email,
            ':given_name' => $this->given_name,
            ':family_name' => $this->family_name,
            ':picture' => $this->picture,
            ':link' => $this->link,
            ':gender' => $this->gender
        ];

        $DB = DB::getInstance();
        $result = $DB->execute($sql, $params);

        if($result)
        {
            return $DB->lastInsertId();
        } else {
            return false;
        }
    }

    function logout()
    {
        session_destroy();
    }
}