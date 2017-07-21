<?php
namespace bookkeeping\Models\Settings;
use \bookkeeping\Models\DB;
use \PDO;

abstract class Setting
{
    use \bookkeeping\Models\Traits\ValidateDate;

    const TABLE_NAME = 'setting';

    public $error_validation;

    function __construct(int $user_id)
    {
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
    }

    /**
     * @return int|id
     */
    public function getSettings(){}

    /**
     * @return bool
     */
    abstract protected function edit($id, $value);

    /**
     * @return bool
     */
    abstract protected static function create($user_id, $value);

    abstract public function setFormat();

    abstract protected function prepareFormat($date);

}