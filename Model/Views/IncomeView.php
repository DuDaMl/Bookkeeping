<?php
namespace bookkeeping\Model\Views;
use bookkeeping\Model\Setting\Setting;
use bookkeeping\Model\Income as M_Income;
use bookkeeping\Model\Category as M_Category;

class IncomeView extends View
{
    protected function __construct()
    {
        $this->controller_name = self::$conttoller;
        $this->current_page = self::$conttoller;
    }

    public function index(Setting $Setting)
    {
        $this->settings = $Setting;
        $this->incomes = M_Income::getAll($Setting);
        $this->categories = M_Category::getAll(self::$conttoller);
        $this->content = $this->render(self::$conttoller . '/Index.php');
        $this->display();
    }

    public function edit()
    {

        // загрузка всех категорий расходов
        $this->categories = M_Category::getAll(self::$conttoller );
        $this->content = $this->render(self::$conttoller . '/Edit.php');
        $this->display();
    }

    public function delete()
    {
        // загрузка всех категорий расходов
        $this->content = $this->render(self::$conttoller . '/Delete.php');
        $this->display();

    }

}