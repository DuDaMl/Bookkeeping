<?php
namespace bookkeeping\Controller;
use bookkeeping\Controller\Controller as Controller;
use bookkeeping\Model\Pay as M_Pay;
use bookkeeping\Model\Category as M_Category;


class Category
    extends Controller
{
    const CONTROLLER_NAME = 'Category';

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $M_Category = new M_Category();

        if(isset($_POST['name']))
        {

            if($M_Category->create($_POST))
            {
                header("Location: /Category/");
                exit();
            }
        }

        $data['categories_pays'] = $M_Category->getAllPays();
        $data['categories_incomes'] = $M_Category->getAllIncomes();
        $data['error'] = $M_Category->error_validation;
        $data['controller_name'] = static::CONTROLLER_NAME;
        $this->content = $this->getView(static::CONTROLLER_NAME . '/Index.php', $data);
        $this->render();
    }

    function edit($id)
    {
        $M_Category = new M_Category();

        if(isset($_POST['name']))
        {
            $M_Category->setId($id);

            if($M_Category->update($_POST))
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

        $data['controller_name'] = static::CONTROLLER_NAME;
        $this->content = $this->getView(static::CONTROLLER_NAME . '/Edit.php', $data);
        $this->render();
    }

    /**
     * @param $id
     */
    function delete($id)
    {
        $M_Category = new M_Category();

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

        $data['controller_name'] = static::CONTROLLER_NAME;
        $this->content = $this->getView(static::CONTROLLER_NAME . '/Delete.php', $data);
        $this->render();
    }
}