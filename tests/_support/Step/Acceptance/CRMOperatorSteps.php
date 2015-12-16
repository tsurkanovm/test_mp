<?php
namespace Step\Acceptance;

class CRMOperatorSteps extends \AcceptanceTester
{
    public function amInAddCustomerUi(){
        $I = $this;
        $I->amOnPage('site/signup');
    }

    public function imagineCustomer(){
        $fake = \Faker\Factory::create();
        return [
            'SignupForm[username]' => $fake->name,
            'SignupForm[email]' => $fake->email,
            'SignupForm[password]' => $fake->password(19),
        ];

    }

    public function fillCustomerDataForm($fieldData){
        $I = $this;
        foreach ($fieldData as $key=>$value) {
            $I->fillField($key,$value);
        }

    }

    public function submitCustomerDataForm(){
        $I = $this;
        $I->click('signup-button');
    }



}