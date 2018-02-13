<?php

namespace App\errors;

/**
 * Class ErrorHandler
 *
 * @package App\errors
 */

class ErrorHandler
{
    /**
     * Checking in all the necessary build in components.
     * @return void
     */
    public function register(): void
    {
        //ini_set('display_errors',1);
        // Display all types of error
        error_reporting(E_ALL);

        // To catch errors like E_CORE_WARNING, E_COMPILE_WARNING, E_STRICT and etc. Checking in the handler function
        set_error_handler([$this, 'errorHandler']);

        // To catch FATAL errors(set_error_handler function doesn't allow to catch and handle FATAL errors).
        // Checking in the handler function
        register_shutdown_function([$this, 'fatalErrorHandler']);

        // To catch exceptions. Checking in the handler function
        set_exception_handler([$this, 'exceptionHandler']);
    }

    /**
     * Errors Handler. Allows us to catch and handle errors like
     * E_CORE_WARNING, E_COMPILE_WARNING, E_STRICT and etc.
     *
     * @param int $errNum Error number
     * @param string $errStr String with an error
     * @param string $errFile File with an error
     * @param int $errLine The number of string with an error
     * @return bool
     */
    public function errorHandler(int $errNum, string $errStr, string $errFile, int $errLine): bool
    {
        //$this->showErrors($errNum, $errStr, $errFile, $errLine);

        // Logging the error information
        $this->logErrors($errNum, $errStr, $errFile, $errLine);

        return true;
    }

    /**
     * FATAL errors Handler. Allows us to catch and handle FATAL errors
     *
     * @return void
     */
    public function fatalErrorHandler(): void
    {
        // Checking the last caught error and checking the type of this error
        if (!empty($error = error_get_last()) && $error['type'] && (E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR)) {
            // Cleaning the output buffer
            ob_get_clean();

            // Displaying the error information
            //$this->showErrors($error['type'], $error['message'], $error['file'], $error['line']);

            // Logging the error information
            $this->logErrors($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }

    /**
     * Exception handler. Allows us to catch and handle all the thrown exceptions
     *
     * @param \Exception $ex
     * @return bool
     */
    public function exceptionHandler(\Exception $ex): bool
    {
        // Displaying the exception information
        //$this->showErrors(get_class($ex), $ex->getMessage(), $ex->getFile(), $ex->getLine());

        // Logging the exception information
        $this->logErrors(get_class($ex), $ex->getMessage(), $ex->getFile(), $ex->getLine());

        return true;
    }

    /**
     * Allows us to display the information about occurred errors
     *
     * @param int $errNum Error number
     * @param string $errStr String with an error
     * @param string $errFile File with an error
     * @param int $errLine The number of string with an error
     * @param int $status Error status
     * @return void
     */
    protected function showErrors(int $errNum, string $errStr, string $errFile, int $errLine, int $status = 500): void
    {
        header("HTTP/1.1 {$status}");
        echo "Error identifier: ", $errNum, '<br>';
        echo "String with an error: ", $errStr, '<br>';
        echo "File with an error: ", $errFile, '<br>';
        echo "The number of string with an error: ", $errLine, '<br>';
    }

    /**
     * Allows us to log all the information about errors or exceptions
     *
     * @param int $errNum Error number
     * @param string $errStr String with an error
     * @param string $errFile File with an error
     * @param int $errLine The number of string with an error
     * @param int $status Error status
     * @return void
     */
    protected function logErrors(int $errNum, string $errStr, string $errFile, int $errLine, int $status = 500): void
    {
        $date = date('Y-m-d H:i:s');
        $f = fopen(ROOT.'/errors/logs/errors.log', 'a');
        $err = "\r\nError identifier: ".$errNum."\r\n";
        $err.="String with an error: ".$errStr."\r\n";
        $err.="File with an error: ".$errFile."\r\n";
        $err.="The number of string with an error: ".$errLine."\r\n";
        $err.="Date: ".$date."\r\n";
        $err.="\r\n=========================================\r\n";
        fwrite($f, $err);
        fclose($f);
    }
}
