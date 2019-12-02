<?php
include_once __DIR__ . '/src/Generator.php';


use GenerateShortRandom\Generator;

$tokens = ['262fe00f25630e58d5a3144e5d625769c2a2ed3e','7d7c018837ea050325ba1f8bc06c1c733f4ac985'];
Generator::initTokens($tokens);

for ($i=1;$i<=10;$i++) {
    $random = Generator::random();
    echo "Random #{$i}: $random\n<br/>";
    sleep(1); //for the delay
}

