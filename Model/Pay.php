<?php
namespace bookkeeping\Model;

use bookkeeping\Model\Setting\Setting;

class Pay extends Model
{
    const TABLE_NAME = 'pay';

    function __construct(){}

    /**
     * return all pays from pointed data limit
     * @param $start
     * @param $end
     * @return array|object
     */
   static function getAll(Setting $setting)
   {
       $sql = "SELECT pay.id, pay.amount, pay.category_id, pay.description, pay.user_id, pay.date, category.name, category.type
               FROM `" . self::TABLE_NAME . "`     
               LEFT JOIN category
               ON pay.category_id = category.id 
               WHERE pay.date BETWEEN  '" . $setting->date_start ."' 
               AND '" . $setting->date_end ."'
               AND pay.user_id = " .  User::getId() . "
               AND category.type = 'Pay'
               ORDER BY date DESC, id DESC
               ";

       $DB = DB::getInstance();
       return $DB->query($sql, static::class);
   }

    /**
     * @param int | $id
     * @return array|bool
     */
    public static function getById(int $id)
    {
        // todo проверка возомжности редактировать данному пользователю данную запись


        $sql = "SELECT * FROM `" . self::TABLE_NAME . "` WHERE  id = " . $id ;
        $DB = DB::getInstance();
        $answer = $DB->query($sql, static::class);
        return $answer;
    }

}