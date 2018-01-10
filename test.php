<pre>
<?php
require 'DPDO.php';
require 'UserConditionKeeper.php';
require 'UserComplexConditionKeeper.php';

DPDO::connect('localhost', 'mysql', 'mysql', 'mailiq');

$userCondition = new UserConditionKeeper();
$userCondition->setState('active', '=');

$userCondition2 = new UserConditionKeeper();
$userCondition2->setCountry('UK', '=');

$userCondition3 = new UserConditionKeeper();
$userCondition3->setFirstname('Elchin', '=');

$condition = new UserComplexConditionKeeper();
$condition->setCondition($userCondition2);
$condition->setCondition($userCondition3, 'OR');

$condition2 = new UserComplexConditionKeeper();
$condition2->setCondition($userCondition);
$condition2->setCondition($condition, 'AND');

//print_r($condition2->getQuery(false));

$memory = memory_get_peak_usage(true);
echo "real: ".($memory/1024/1024)." MiB\n\n";
$begin = microtime(true);

$condition2->executeAll();

$end = microtime(true);
print "\nExecution time: " . ($end - $begin) . " sec\n";
$memory2 = memory_get_peak_usage(true);
echo "memory used: ".(($memory2-$memory1)/1024/1024)." MiB\n\n";

