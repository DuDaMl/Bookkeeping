<?php
namespace bookkeeping\Model\Exceptions;

/**
 * Адрес не найден в базе адресов
 */
class DateNotFilledException extends \Exception
{
    /**
     * Не найденный адрес
     * @var Address
     */
    //private $address;

    /**
     * @param Address $address
     */
    /*public function __construct(Address $address)
    {
        Exception::__construct('Не найден адрес '.$address->oneLine);
        $this->address = $address;
    }*/
    /**
     * @return Address
     */
    /*
    public function getAddress()
    {
        return $this->address;
    }
    */
}