<?php
namespace bookkeeping\Controllers;
use bookkeeping\Models\Pay as M_Pay;
use bookkeeping\Models\Category as M_Category;


class Category
{
    const MAIN_TEAMPLATE = 'Category';
    private $M_Category;

    public function __construct()
    {
        echo " __construct Controller Pay <br/>";
        $this->M_Category = new M_Category();
    }

    public function index()
    {

    }
}