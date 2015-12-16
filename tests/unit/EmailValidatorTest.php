<?php
namespace tests\unit;

use Codeception\TestCase\Test;
use yii\validators\EmailValidator;

class EmailValidatorTest extends  Test{

    /**
     * @dataProvider getEmailVariants
     */
    public function testEmail($email, $result){
        $validator = new EmailValidator();
        $this->assertEquals($validator->validate($email), $result);
    }

    public function getEmailVariants(){
        return [
            ['test@test.com', true],
            ['test@test', true],
            ['testtest.com', false]
        ];
    }
}