<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) exit;

/**
 * The MXESDMErrorHandle class.
 *
 * Error Handling.
 */
class MXESDMErrorHandle
{

    /**
    * Has an error.
    */
    public $errorInstance = true;
    
    public function classAttributesError( $className, $method )
    {

        // If class not exists display an error.
        if (class_exists($className)) {

            // Check if a method exists.
            $classInstance = new $className();

            // If a method not exists display an error.
            if (!method_exists($classInstance, $method)) {

                // Error notice.
                $errorNotice = "The <b>\"{$className}\"</b> class doesn't contain the <b>\"{$method}\"</b> method.";

                // Show an error.
                $errorMethodInstance = new MXESDMDisplayError( $errorNotice );

                $errorMethodInstance->showError();

                $this->errorInstance = $errorNotice;
            }

        } else {

            // Notice of error.
            $errorNotice = "The <b>\"{$className}\"</b> class not exists.";

            // Show an error.
            $errorClassInstance = new MXESDMDisplayError( $errorNotice );

            $errorClassInstance->showError();

            $this->errorInstance = $errorNotice;
        }
    
        return $this->errorInstance;
    }
    
}
