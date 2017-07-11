<?php
namespace bookkeeping\Models\Settings;
use \bookkeeping\Models\DB;
use \PDO;

abstract class Setting
{
    use \bookkeeping\Models\Traits\ValidateDate;

    const TABLE_NAME = 'setting';
    private $id;
    protected static $user_id;
    protected static $controller;
    public $value;

    public $error_validation;

    function __construct(string $controller, int $user_id)
    {
        static::$user_id = $user_id;
        static::$controller = $controller;
    }

    /**
     * Возвращает значение статической переменной
     * @return mixed
     */
    static function getController()
    {
        return static::$controller;
    }

    /**
     * Возвращает значение статической переменной
     * @return mixed
     */
    static function getUserId()
    {
        return static::$user_id;
    }

    /**
     * @return int|id
     */
    abstract public function getSettings();

    /**
     * @return bool
     */
    abstract protected function edit($id, $value);

    /**
     * @return bool
     */
    abstract protected function create($value);

    abstract public function setFormat();

    abstract protected function prepareFormat();

}