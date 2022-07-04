<?php
namespace OSOLCCC;
Class SingletonParent{
	 protected static $instances =  array();// to solve  the issue https://stackoverflow.com/questions/17632848/php-sub-class-static-inheritance-children-share-static-variables
    /**
     *  @brief Singleton Constructor
     *  
     *  @return ClassInstance
     *  
     *  @details Caution: never call Class::getInstance() in another class's constructor, that instance will be discarded from $instances array 
     */
    public static function getInstance()// Caution: never call Class::getInstance() in another class's constructor
	{        
        //https://www.php.net/manual/en/reflectionclass.newinstancewithoutconstructor.php &
        //https://refactoring.guru/design-patterns/singleton/php/example
        $ref  = new \ReflectionClass( get_called_class() ) ;         
        $reflectionProperty = new \ReflectionProperty(static::class, 'instances');
        $reflectionProperty->setAccessible(true);
        //echo $reflectionProperty->getValue();
        $instances =   $reflectionProperty->getValue();;//$reflectedClass->getStaticPropertyValue('inst');
		$intentedClass = static::class;
        if (  !isset($instances[$intentedClass]))
        {
            // The magic.
            //$ctor->setAccessible( true ) ;
            //$inst = new static();
            $instances[$intentedClass] = new static();
            //echo "INSTANTIATED ".print_r($inst,true) ."<br />";
             
            $reflectionProperty->setValue(null/* null for static var */, $instances);
            //echo "<pre>". print_r(array_keys($instances),true)."</pre>";
        }		
        return $instances[$intentedClass] ;
    }//public static function getInstance()
}//Class Frontend{
?>