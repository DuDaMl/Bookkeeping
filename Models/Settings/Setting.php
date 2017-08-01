<?php
namespace bookkeeping\Models\Settings;
use \bookkeeping\Models\DB;
use \PDO;

abstract class Setting
{
    use \bookkeeping\Models\Traits\ValidateDate;

    const TABLE_NAME = 'setting';
    public $error_validation;

    function __construct(int $user_id){}
}