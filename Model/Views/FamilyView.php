<?php
namespace bookkeeping\Model\Views;
use bookkeeping\Model\Setting\Setting;
use bookkeeping\Model\Family;
use bookkeeping\Model\User;


class FamilyView extends View
{

    protected function __construct()
    {
        $this->controller_name = self::$conttoller;
        $this->current_page = self::$conttoller;
    }

    public function index()
    {

        $this->incomig_request = Family::getIncomeRequest();
        $this->waiting_request = Family::getSendedRequest();
        $this->confirmed_request = Family::getConfirmedRequest();
        $this->content = $this->render(self::$conttoller . '/Index.php');
        $this->display();
    }

    public function confirm()
    {
        // загрузка всех категорий расходов

        $this->content = $this->render(self::$conttoller . '/Confirme.php');
        $this->display();
    }

    public function delete()
    {
        // загрузка всех категорий расходов
        $this->content = $this->render(self::$conttoller . '/Delete.php');
        $this->display();

    }

}