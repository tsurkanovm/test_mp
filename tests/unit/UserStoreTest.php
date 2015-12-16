<?php
namespace tests\unit;
use common\components\UserStore;

use Yii;

class UserStoreTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    private $store;

    public function _before()
    {
        $this->store = new UserStore();
    }

    public function imagineCustomer(){
        $fake = \Faker\Factory::create();
        return [
            'name' => $fake->name,
            'email' => $fake->email,
            'pass' => $fake->password(19),
        ];

    }

    public function testGetUser(){
        $imagineUser = $this->imagineCustomer();
        $this->store->addUser($imagineUser['name'],$imagineUser['email'],$imagineUser['pass']);
        $user = $this->store->getUser($imagineUser['email']);
        $this->assertEquals($user['name'], $imagineUser['name']);
        $this->assertEquals($user['email'], $imagineUser['email']);
        $this->assertEquals($user['pass'], $imagineUser['pass']);
    }

    public function testAddUser_ShortPass(){
        $this->setExpectedException('\yii\base\Exception');
        $this->store->addUser('Some Name','collmail@gig.com','ff');
    }

    protected function _after()
    {
    }

    // tests
//    public function testMe()
//    {
//
//    }
}