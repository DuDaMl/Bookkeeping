<?php
namespace bookkeeping\Core;
/**
 * Class ErrorHandlre
 * @package bookkeeping\Core
 */

class ErrorHandler
{
    public function register()
    {
        set_error_handler([$this, 'errorHeadrler']);
        register_shutdown_function([$this, 'fatalErrorHandler']);
        set_exception_handler([$this, 'exceptionHandler']);
    }

    function errorHeadrler($errno, $errstr, $file, $line)
    {
        $this->showError($errno, $errstr, $file, $line);
        return true;
    }

    /**
     * Метод, который фиксирует наличие фатальной ошибки
     * и обрабатывает ее.
     */
    public function fatalErrorHandler()
    {
        // если в буфере находим фатальную ошибку,
        $error = error_get_last();

        /*if (
            // если в коде была допущена ошибка
            is_array($error) &&
            // и это одна из фатальных ошибок
            in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])
        )*/
        //if ($error = error_get_last() AND $error['type'] && (E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR))
        if (! empty($error = error_get_last()) && $error['type'] & (E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR))
        {
            // сбросить буфер, завершить работу буфера
            ob_end_clean();

            // то выводим ее в браузере
            $this->showError($error['type'], $error['message'], $error['file'], $error['line'], 500);
        }
    }

    /**
     * Метод, который будет обрабатывать исключения,
     * вызванные вне блока try/catch
     *
     * @param \Exception $e
     */
    public function exceptionHandler(\Exception $e)
    {
        // выводим информацию об исключении в браузере
        $this->showError(get_class($e), $e->getMessage(), $e->getFile(), $e->getLine(), 404);
    }
    /**
     * Метод, который фиксирует наличие фатальной ошибки
     * и обрабатывает ее.
     */

    /**
     * Храним здесь обрабатываемые ошибки,
     * и возвращаем, в зависимости от кода, название ошибки
     *
     * @param $error
     * @return string
     */
    static public function getErrorName($error){
        $errors = [
            E_ERROR             => 'ERROR',
            E_WARNING           => 'WARNING',
            E_PARSE             => 'PARSE',
            E_NOTICE            => 'NOTICE',
            E_CORE_ERROR        => 'CORE_ERROR',
            E_CORE_WARNING      => 'CORE_WARNING',
            E_COMPILE_ERROR     => 'COMPILE_ERROR',
            E_COMPILE_WARNING   => 'COMPILE_WARNING',
            E_USER_ERROR        => 'USER_ERROR',
            E_USER_WARNING      => 'USER_WARNING',
            E_USER_NOTICE       => 'USER_NOTICE',
            E_STRICT            => 'STRICT',
            E_RECOVERABLE_ERROR => 'RECOVERABLE_ERROR',
            E_DEPRECATED        => 'DEPRECATED',
            E_USER_DEPRECATED   => 'USER_DEPRECATED',
        ];
        if(array_key_exists($error, $errors)){
            return $errors[$error] . " [$error]";
        }
        return $error;
    }

    /**
     * Вспомогательный метод,
     * который выводит информацию о случившемся в виде текста в браузере
     *
     * @param $errno
     * @param $errstr
     * @param $file
     * @param $line
     * @param int $status
     */
    public function showError($errno, $errstr, $file, $line, $status = 500)
    {
        header("HTTP/1.1 $status");
        echo $message = '<b>' . self::getErrorName($errno) . "</b><hr>" . $errstr . '<hr> file: ' . $file . '<hr> line: ' . $line . '<hr>';
        echo '<br>';
    }

}