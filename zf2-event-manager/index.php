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

class ValidatePost
{
    public function validate($event)
    {
        $data = $event->getParams();
        
        if (!isset($data['title']) or !isset($data['slug'])) {
            throw new \InvalidArgumentException('Need a title and a slug!');
        }
        
        return $data;
    }
}

class SlugifyPost
{
    public function slugify($event)
    {
        $data = $event->getParams();
        
        $event->setParam('slug', strtolower(str_replace(' ', '-', $data['title'])));
        
        return $data;
    }
}

class Post
{
    protected $events = null;
    
    public function events()
    {
        if ($this->events === null) {
            $this->events = new EventManager(__CLASS__);
            
            $this->events->attach('save', array('SlugifyPost', 'slugify'), 100);
            $this->events->attach('save', array('ValidatePost', 'validate'), 90);
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

$post = new Post;

$values = array(
    'title' => 'My post'
);

try {
    $post->save($values);
} catch(\InvalidArgumentException $e) {
    echo $e->getMessage();
}
