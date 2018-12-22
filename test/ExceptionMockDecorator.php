<?php

/**
 * A decorator that enables SimpleTest mock objects to throw exceptions.
 *
 * @see http://osdir.com/ml/php.simpletest.general/2005-04/msg00052.html
 */
class ExceptionMockDecorator {

   private $mock;

   public function __construct($mock) {
        $this->mock = $mock;
   }

   function __call($method,$args) {
       $return = call_user_func_array(array($this->mock,$method),$args);
       if ($return instanceof Exception) throw $return;
       return $return;
   }

}
