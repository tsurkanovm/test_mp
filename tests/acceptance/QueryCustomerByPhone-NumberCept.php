<?php 
$I = new \Step\Acceptance\CRMOperatorSteps($scenario);
$I->wantTo('add differenta customers to database');
$I->amInAddCustomerUi();

$first_customer = $I->imagineCustomer();
$I->fillCustomerDataForm($first_customer);
$I->submitCustomerDataForm();
