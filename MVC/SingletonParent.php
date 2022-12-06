<?php
/** 
 *  \namespace OSOLCCC 
 *  \brief     Parent name space of all sub namespaces of this project.
 *  \details   This namespace is the root namespace.\n
 * Holds all classes &amp; subclasses for paginator.\n
 * This documentation is written in OSOLCCC::SingletonParent under *namespace* tag\n
 * And will be shown in Main Project &gt;&gt; Namespaces &gt;&gt; Namespaces List &gt;&gt; thisNamespacename 
 *  \author    Sreekanth Dayanand
 *  \author    [Outsource Online Internet Solutions](https://www.outsource-online.net)
 *  \copyright GNU Public License. 
 */
 
 /*! 
 *	\namespace OSOLCCC::ExtraClasses
 *  \brief	Holds OSOLmulticaptcha which shows captcha.
 *  \details   This namespace is the namespace holding important classes (OSOLPageNav &amp; OSOLMySQL) of this project.\n
 * This documentation is written in OSOLCCC::SingletonParent under *namespace* tag\n
 * And will be shown in Main Project &gt;&gt; Namespaces &gt;&gt; &gt;&gt; Namespaces List &gt;&gt; thisNamespacename 
 *  \author    Sreekanth Dayanand
 *  \author    [Outsource Online Internet Solutions](https://www.outsource-online.net)
 *  \copyright GNU Public License.
 */
 
 /*!
 \class OSOLCCC::SingletonParent
 \brief Parent class to initiate and return singleton instances for any subclass.\n
 \details  This documentation is written in OSOLCCC::SingletonParent under *class* tag\n
  And will be shown in Main Project &gt;&gt; Data Structures &gt;&gt; Nampespace &gt;&gt; thisClassName
 \details   Which ever class needs to be made singleton may subclass it\n
 \par Usage:
 ```
 //assuming class OSOLCCC::ExtraClasses::OSOLmulticaptcha extends SingletonParent
 $captcha = OSOLCCC::ExtraClasses::OSOLmulticaptcha::getInstance();
 ```
 *  \author    Sreekanth Dayanand
 *  \author    [Outsource Online Internet Solutions](https://www.outsource-online.net)
 *  \version   0.0.1
 *  \date      2022-2032
 *  \pre 
 1. PHP 7+ is required
 2. If not autoloaded, Class Files must be explicitly included
 
 *  \bug       No bugs found till July,2022.
 *  \warning   Improper use can crash your application
 *  \copyright GNU Public License.
*/
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