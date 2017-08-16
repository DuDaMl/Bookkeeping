<?php
namespace bookkeeping\Controller;
use bookkeeping\Controller\Controller as Controller;
use bookkeeping\Model\Pay as M_Pay;
use bookkeeping\Model\Category as M_Category;
use bookkeeping\Model\Views\View as M_View;

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

        // Создание объекта представления для контроллера
        $M_View = M_View::getInstance(static::CONTROLLER_NAME);
        $M_View->user = $this->user;
        $M_View->index();

    }

    //todo обработка несуществующих, не принадлежащих пользователю категорий
    function edit(int $id)
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

        // Создание объекта представления для контроллера
        $M_View = M_View::getInstance(static::CONTROLLER_NAME);
        $M_View->category = $M_Category->getById($id)[0];
        $M_View->user = $this->user;
        $M_View->edit();
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

        // Создание объекта представления для контроллера
        $M_View = M_View::getInstance(static::CONTROLLER_NAME);
        $M_View->category = $M_Category->getById($id)[0];
        $M_View->user = $this->user;
        $M_View->delete();
    }
}