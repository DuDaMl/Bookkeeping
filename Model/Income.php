<?php
namespace bookkeeping\Model;
use bookkeeping\Model\Setting\Setting;

class Income extends Model
{
    const TABLE_NAME = 'income';

    function __construct(){}

    /**
     * @return array|bool
     */
   static function getAll(Setting $setting)
   {
       $sql = "SELECT income.id, income.amount, income.user_id, income.category_id, income.description, income.date, category.name, category.type
               FROM `" . self::TABLE_NAME . "`     
               LEFT JOIN category
               ON income.category_id = category.id 
               WHERE income.date BETWEEN  '" . $setting->date_start ."' 
               AND '" . $setting->date_end ."'  
               AND category.type = 'Income'
               AND income.user_id = " .  User::getId() . "
               ORDER BY date DESC, id DESC
               ";

       $DB = DB::getInstance();
       return $DB->query($sql, static::class);
   }

    /**
     * @return array|bool
     */
    static function getById(int $id)
    {
        $sql = "SELECT * FROM `" . self::TABLE_NAME . "` WHERE  id = " . $id;
        $DB = DB::getInstance();
        $answer = $DB->query($sql, static::class);
        return $answer;
    }

}