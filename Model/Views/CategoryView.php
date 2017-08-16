<?php
namespace bookkeeping\Model\Views;
use bookkeeping\Model\Setting\Setting;
use bookkeeping\Model\Category as M_Category;


class CategoryView extends View
{

    protected function __construct()
    {
        $this->controller_name = self::$conttoller;
        $this->current_page = self::$conttoller;
    }

    public function index()
    {
        $this->categories_pays = M_Category::getAllPays();
        $this->categories_incomes = M_Category::getAllIncomes();
        $this->content = $this->render(self::$conttoller . '/Index.php');
        $this->display();
    }

    public function edit()
    {
        // загрузка всех категорий расходов

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