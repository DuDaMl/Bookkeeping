<?php
namespace bookkeeping\Model\Traits;

trait ValidateInt
{
    function validateInt($var)
    {
        if(! filter_var($var, FILTER_VALIDATE_INT)) {

            $this->error_validation = array(
                'error' => true,
                'amount' => 'Ошибка в указанном ' . $var,
            );
            return false;
        }
        return $var;
    }
}