<?php
namespace bookkeeping\Model\Traits;
use \DateTime;

trait ValidateDate
{
    /**
     * @param $date
     * @param string $format
     * @return bool false если формат не верен, дату в формате если верно
     */
    function validateDate($date, $format = 'Y-m-d')
    {
        trim($date);
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}