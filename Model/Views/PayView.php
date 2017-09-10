<?php
namespace bookkeeping\Model\Views;
use bookkeeping\Model\Setting\Setting;
use bookkeeping\Model\Pay as M_Pay;
use bookkeeping\Model\Category as M_Category;


class PayView extends View
{

    protected function __construct()
    {
        $this->controller_name = self::$conttoller;
        $this->current_page = self::$conttoller;
    }

    public function index(Setting $Setting)
    {
        $this->settings = $Setting;
        $this->pays = M_Pay::getAll($Setting);
        $this->categories = M_Category::getAll(self::$conttoller);
        $this->js = ["https://www.gstatic.com/charts/loader.js" ,
                     "/assets/js/pay/index.js"];
        $this->chartData = M_Pay::getChartData($Setting);
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