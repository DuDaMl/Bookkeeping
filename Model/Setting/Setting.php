<?php
namespace bookkeeping\Model\Setting;

abstract class Setting
{
    use \bookkeeping\Model\Traits\ValidateDate;

    const TABLE_NAME = 'setting';


    /**
     * Загрузка настроек по указаному User::getId
     * Инициализация user_id
     * @param int $user_id - id авторихзированного пользователя
     */
    abstract function __construct();

    public static function getInstance($type)
    {
        switch($type)
        {
            case 'Pay':
                return new PaySetting();
                break;
            case 'Income':
                return new IncomeSetting();
                break;
        }
    }

    /**
     * @return object | Обьект с полями значений настроек
     */
    abstract public function get();

    /**
     * Обновление настроек в БД
     * @return bool
     */
    abstract public function update($date);

    /**
     * Создание новой записи параметров для определенного контроллера и авторизированного пользователя
     */
     abstract public function create($date);

    /**
     * Функция подготовки параметров настроек контроллера перед сохранением в БД
     * @return bool
     */
    abstract protected function prepareFormat(array $date);

    abstract protected function setDefault();

}