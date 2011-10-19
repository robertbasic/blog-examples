<?php

use Zend\EventManager\EventManager;

define('ZF2_PATH',
    (getenv('ZF2_PATH') ? getenv('ZF2_PATH') : realpath(__DIR__ . '/library/Zend'))
);

require_once ZF2_PATH . '/Loader/StandardAutoloader.php';
$autoLoader = new Zend\Loader\StandardAutoloader();

// register our StandardAutoloader with the SPL autoloader
$autoLoader->register();

// register the Zend namespace
$autoLoader->registerNamespace('Zend', ZF2_PATH);

class ValidateFoo
{
    public function isValid($event)
    {
        $data = $event->getParams();
        
        if (count($data) != 5) {
            throw new \InvalidArgumentException('You need to pass an array with 5 elements!');
        }
        
        return $data;
    }
}

class MangleFoo
{
    public function mangle($event)
    {
        $data = $event->getParams();
        
        foreach ($data as $key => $value) {
            $event->setParam($key, $value * 2);
        }
        
        return $data;
    }
}

class Foo
{
    protected $events = null;
    
    public function events()
    {
        if ($this->events === null) {
            $this->events = new EventManager(__CLASS__);
            
            $this->events->attach('save', array('MangleFoo', 'mangle'), 100);
            $this->events->attach('save', array('ValidateFoo', 'isValid'), 90);
        }
        
        return $this->events;
    }
    
    public function save($data)
    {
        // this prepareArgs method allows to call the 
        // setParam method in the MangleFoo::mangle method
        // so that ValidateFoo::isValid has the mangle'd data
        $this->events()->prepareArgs($data);
        $response = $this->events()->trigger('save', $this, $data);
        
        echo 'data saved! ' . json_encode($response->last());
    }
}

$foo = new Foo;

$values = array(1, 2, 3, 4, 5);

try {
    $foo->save($values);
} catch(\InvalidArgumentException $e) {
    echo $e->getMessage();
}
