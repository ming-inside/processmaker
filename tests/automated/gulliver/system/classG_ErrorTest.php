<?php 
  require_once PATH_TRUNK . 'gulliver/thirdparty/smarty/libs/Smarty.class.php'; 
  require_once PATH_TRUNK . 'gulliver/system/class.xmlform.php'; 
  require_once PATH_TRUNK . 'gulliver/system/class.xmlDocument.php'; 
  require_once PATH_TRUNK . 'gulliver/system/class.error.php'; 

  /** 
   * Generated by ProcessMaker Test Unit Generator on 2012-05-10 at 20:39:58.
  */ 

  class classG_ErrorTest extends PHPUnit_Framework_TestCase 
  { 
    /**
    * @covers G_Error::G_Error
    * @todo   Implement testG_Error().
    */
    public function testG_Error() 
    { 
        if (class_exists('G_Error')) {
             $methods = get_class_methods( 'G_Error');
            $this->assertTrue( in_array( 'G_Error', $methods ), 'seems like this function is outside this class' ); 
        } 
    } 

    /**
    * @covers G_Error::errorMessage
    * @todo   Implement testerrorMessage().
    */
    public function testerrorMessage() 
    { 
        if (class_exists('G_Error')) {
             $methods = get_class_methods( 'G_Error');
            $this->assertTrue( in_array( 'errorMessage', $methods ), 'seems like this function is outside this class' ); 
        } 
    } 

  } 