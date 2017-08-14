<?php
namespace bookkeeping\Model\Views;
use bookkeeping\Model\Views\PayView;


class View
implements \Countable
{
    protected $data = [];
    protected static $conttoller;

    public static function getInstance($type)
    {
        self::$conttoller = $type;

        switch($type)
        {
            case 'Pay':
                return  new PayView();
                break;
            case 'Income':
                return new IncomeView();
                break;
        }
    }

    public function __set($k, $v)
    {
        $this->data[$k] = $v;
    }

    public function __get($k)
    {
        return $this->data[$k];
    }

    public function render($template)
    {
        ob_start();
        foreach ($this->data as $prop => $value) {
            $$prop = $value;
        }
        include 'View/' . $template;
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    public function display()
    {
        echo $this->render('/Main.php');
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->data);
    }
}