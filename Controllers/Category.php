<?php
namespace bookkeeping\Controllers;
use bookkeeping\Controllers\Controller as Controller;
use bookkeeping\Models\Pay as M_Pay;
use bookkeeping\Models\Category as M_Category;


class Category
    extends Controller
{
    const CONTROLLER_NAME = 'Category';
    protected static $main_teamplate = 'Category';

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $M_Category = new M_Category($this->user->getId());

        if(isset($_POST['name']))
        {
            // Array ( [name] => 123 [type] => Pay )
            $M_Category->name = $_POST['name'];
            $M_Category->type = $_POST['type'];

            if($M_Category->create())
            {
                header("Location: /Category/");
                exit();
            }
        }

        $data['categories_pays'] = $M_Category->getAllPays();
        $data['categories_incomes'] = $M_Category->getAllIncomes();
        $data['error'] = $M_Category->error_validation;

        $data['controller_name'] = self::getMainTeamplate();
        $this->content = $this->getView(self::getMainTeamplate() . '/Index.php', $data);
        $this->render();
    }

    function edit($id)
    {
        $M_Category = new M_Category($this->user->getId());

        if(isset($_POST['name']))
        {
            $M_Category->setId($id);

            if($M_Category->update())
            {
                header("Location: /Category/");
                exit();
            }
            $data['error'] = $M_Category->error_validation;
        }

        $data['category'] = $M_Category->getById($id)[0];

        if(empty($data['category']))
        {
            if(! empty($data['error']))
            {
                $data['error']['text'] = $data['error']['text'] . ' <br/> нет такой записи';
            } else {
                $data['error'] =  array(
                    'error' => true,
                    'text' => 'нет такой записи',
                );
            }
        }

        $data['controller_name'] = self::getMainTeamplate();
        $this->content = $this->getView(self::getMainTeamplate() . '/Edit.php', $data);
        $this->render();
    }

    /**
     * @param $id
     */
    function delete($id)
    {
        $M_Category = new M_Category($this->user->getId());

        if(!empty($_POST) && $_POST['id'] != '')
        {
            $M_Category->setId($id);

            if($M_Category->delete()){
                header("Location: /Category/");
                exit();
            }
        }

        $data['category'] = $M_Category->getById($id)[0];
        $data['error'] = $M_Category->error_validation;

        if(empty($data['category']))
        {
            if(! empty($data['error']))
            {
                $data['error']['text'] = $data['error']['text'] . ' <br/> нет такой записи';
            } else {
                $data['error'] =  array(
                    'error' => true,
                    'text' => 'нет такой записи',
                );
            }
        }

        $data['controller_name'] = self::getMainTeamplate();
        $this->content = $this->getView(self::getMainTeamplate() . '/Delete.php', $data);
        $this->render();
    }
}