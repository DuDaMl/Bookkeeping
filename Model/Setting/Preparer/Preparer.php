<?php
namespace bookkeeping\Model\Setting\Preparer;

use bookkeeping\Model\Setting\Setting;

abstract class Preparer
{

    use \bookkeeping\Model\Traits\ValidateDate;

    public static function getInstance($type)
    {
        $class = new \ReflectionClass($type);
        $class_name = $class->getShortName();

        switch($class_name)
        {
            case 'PaySetting':
                return new PayPreparer();
                break;
            case 'IncomeSetting':
                return new IncomePreparer();
                break;
        }
    }

    abstract function prepare(Setting $obj, array $date);

}