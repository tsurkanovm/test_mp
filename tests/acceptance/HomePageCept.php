<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('to see home page');
$I->amOnPage('/backend/web/index.php?r=parser%2Findex');
$I->see('Choose file to parse','h3');

