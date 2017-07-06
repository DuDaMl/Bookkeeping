<?php
namespace bookkeeping\Models\Settings;
use \DateTime;
use \PDO;
use bookkeeping\Models\Setting;

class PaySetting extends Setting
{
    function __construct( )
    {
    }

    public function setFormat(array $params)
    {
        // валидация введенных дат по переданной маске из контроллера
        if(! $this->validateDate($params['date_start'], 'Y-m-d'))
        {
            // в случае неправльно указанных данных устанавливается значение по умолчанию
            $params['date_start']= date('Y-m-01');
            $params['date_end'] = date('Y-m-31');
        }

        // проверка существования записи для выбора действия INSERT/DELETE
        $controller = self::getByController();

        if(empty($controller))
        {
            // create
            $result = $this->create(serialize($params));
            print_r($result);
            $this->error_validation .= " no insert";

        } else {
            // update
            $result = $this->edit($controller->id, serialize($params));
            $this->error_validation .= " no update";
        }
    }
}