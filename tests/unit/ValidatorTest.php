<?php
namespace tests\unit;

use common\components\Validator;
use Yii;

class ValidatorTest extends \Codeception\TestCase\Test
{
    public  function testValidate_FalsePass(){
        $store = $this->getMock('common\components\UserStore');
        $this->validator = new Validator($store);

        
        $store->expects($this->once())
            ->method('notifyPasswordFailure')
            ->with($this->equalTo("test@emails.com"));

        $store->expects($this->any())
            ->method("getUser")
            ->will($this->returnValue([
                "name"=>"fdsfdf",
                "email"=>"test@emails.com",
                "pass"=>"rihfhh"
            ]));

        $this->assertFalse($this->validator->validateUser("test@emails.com", "wrong"));
    }
}