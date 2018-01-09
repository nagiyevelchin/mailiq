<pre>
<?php
require 'DPDO.php';
require 'UserConditionKeeper.php';
require 'UserComplexConditionKeeper.php';

DPDO::connect('localhost', 'mysql', 'mysql', 'mailiq');

print '<h3>(ID = 1000) ИЛИ (Страна != Россия)</h3>
$userCondition = new UserConditionKeeper();
$userCondition->setId(1000, \'=\');
$userCondition->setCountry(\'Россия\', \'!=\', \'OR\');

';
$userCondition = new UserConditionKeeper();
$userCondition->setId(1000, '=');
$userCondition->setCountry('Россия', '!=', 'OR');

print_r($userCondition->getQuery());

$begin = microtime(true);
$userCondition->execute();
$end = microtime(true);
print "\nExecution time: " . ($end - $begin) . " sec\n";

/*while($arr = $userCondition->fetch()){
	print_r($arr);
}*/

//----------------------------------------------------------------------------------------------------------------------

print '<hr><h3>(Страна = Россия) И (Состояние пользователя != active)</h3>
$userCondition2 = new UserConditionKeeper();
$userCondition2->setCountry(\'Россия\', \'=\');
$userCondition2->setState(\'active\', \'!=\');

';
$userCondition2 = new UserConditionKeeper();
$userCondition2->setCountry('Russia', '=');
$userCondition2->setState('active', '!=');

print_r($userCondition2->getQuery());

$begin = microtime(true);
$userCondition2->execute();
$end = microtime(true);
print "\nExecution time: " . ($end - $begin) . " sec\n";

/*while($arr = $userCondition2->fetch()){
	print_r($arr);
}*/

//----------------------------------------------------------------------------------------------------------------------

print '<hr><h3>((Страна != Россия) ИЛИ (Состояние пользователя = active)) И (E-Mail = user@domain.com)</h3>
$userCondition3 = new UserConditionKeeper();
$userCondition3->setState(\'active\', \'=\');
$userCondition3->setCountry(\'Россия\', \'!=\', \'OR\');

$userCondition4 = new UserConditionKeeper();
$userCondition4->setEmail(\'user@domain.com\', \'=\');

$condition = new UserComplexConditionKeeper();
$condition->setCondition($userCondition3);
$condition->setCondition($userCondition4);

';

$userCondition3 = new UserConditionKeeper();
$userCondition3->setState('active', '=');
$userCondition3->setCountry('Russia', '!=', 'OR');

$userCondition4 = new UserConditionKeeper();
$userCondition4->setEmail('elchin@nagiyev.net470507', '=');

$condition = new UserComplexConditionKeeper();
$condition->setCondition($userCondition3);
$condition->setCondition($userCondition4);

print_r($condition->getQuery());

$begin = microtime(true);
$condition->execute();
$end = microtime(true);
print "\nExecution time: " . ($end - $begin) . " sec\n";

/*while($arr = $condition->fetch()){
	print_r($arr);
}*/

//----------------------------------------------------------------------------------------------------------------------

print '<hr><h3>(((Страна != Россия) ИЛИ (Состояние пользователя = active)) И (E-Mail = user@domain.com)) ИЛИ (Role != user)</h3>
$userCondition5 = new UserConditionKeeper();
$userCondition5->setRole(\'user\', \'!=\');

$condition2 = new UserComplexConditionKeeper();
$condition2->setCondition($condition);
$condition2->setCondition($userCondition5,\'OR\');

';
$userCondition5 = new UserConditionKeeper();
$userCondition5->setRole('user', '!=');

$condition2 = new UserComplexConditionKeeper();
$condition2->setCondition($condition);
$condition2->setCondition($userCondition5, 'OR');

print_r($condition2->getQuery());

$begin = microtime(true);
$condition2->execute();
$end = microtime(true);
print "\nExecution time: " . ($end - $begin) . " sec\n";

/*
while($arr = $condition2->fetch()){
	print_r($arr);
}*/
