<?php
    //show all errors - useful whilst developing
    error_reporting(E_ALL);

function initKeys(){
	global $appID,$devID,$certID,$RuName,$serverUrl, $userToken,$compatabilityLevel, $siteID;
    // these keys can be obtained by registering at http://developer.ebay.com
    $production         = true;   // toggle to true if going against production
    $compatabilityLevel = 893;    // eBay API version
	$siteID = 0; // 0 = US
    if ($production) {
        $devID = '1b5ff2f1-fa67-4fc8-92df-0f9d45dae0b2';   // these prod keys are different from sandbox keys
        $appID = 'SpektraM-86c9-4eff-8e26-f708893febd3';
        $certID = '3a7805fb-a3cc-45c7-8640-95a66ce663a0';
        $RuName = '';
        //set the Server to use (Sandbox or Production)
        $serverUrl = 'https://api.ebay.com/ws/api.dll';      // server URL different for prod and sandbox
        //the token representing the eBay user to assign the call with
        $userToken = 'AgAAAA**AQAAAA**aAAAAA**sG5nVA**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6AFkIKoAZSDowmdj6x9nY+seQ**sZICAA**AAMAAA**XaKR1ET7r30QpkUaddPE3bL+NSz7+MD3mK9XGtJdwiFP+mRI00eZTOmi4DeXAOcplpyhZtLhrE3Z5IFsHqck2ZJrsZvtRgjkpt1IMtUEeAa/y5X8VhjAtdpdAjeAPlyDH01OYMLZl3SiQ84mcmluKZ7xRmaYFjc3+RRBI9yYqWgLOImCw0X6pA59S8sWB0M3lBtd2dpyLWqwc2IBOrVJNg0Tf/JSM3eEW0GFuljdXmsEdD4tiELYUr/goRuAwwUBD6F9wUiDTt8zkX9XBcTu//0666Xbr71OF7kb5YI/+cGixe76O5i9XUd35F57Qg9FrAIc/9Hzg53uvu736UGIyakX0hfKfqZOzlOaiKnfdnTT+TLcAeDOMZsa38rQPBAWCLI7l3RAfs8byF3Fpwu3XJ3ucgv7m3kTOhmzCHkSYzI9f9f2Reje3105ibB5f3j6ILFC1tfnFfVp99mzXxrl4bL/3YIkN7DxM4+INkka4QK6h4EE58SnSC9mRoF7VsjmcuNIMXlXnoFNqZy5WRjMI4lhkoah1TNT00WGHj77KmdY7rgb8vKEp+BAiDLVvJV3foqdsHEEkaX0NxFR/TLBT/ibT6np5wTlMSxQno/qbLB0jOyHP74DwXm0rm2bOHrQmI30DNkD7J7XAruNKnNcYZ3rhfcXlwifzVFqnknC4udiG5jtQFyqCCmB1uYHnpyWUJjQNAJYjcuHscwsUf//4AQ/hbwthUxV/zp6dWveaz43WeZnpnHfotFgq0UbZIop';
    } else {
        // sandbox (test) environment
        $devID = 'f2a7f4ed-c06f-494d-bc2e-af34346a2705';   // these prod keys are different from sandbox keys
        $appID = 'SpektraM-22c1-4033-9474-b82d87789d76';
        $certID = 'd09dc03e-7fa4-4a81-b031-115bafec350d';
        //set the Server to use (Sandbox or Production)
        $serverUrl = 'https://api.sandbox.ebay.com/ws/api.dll';
        // the token representing the eBay user to assign the call with
        // this token is a long string - don't insert new lines - different from prod token
        $userToken = 'AgAAAA**AQAAAA**aAAAAA**kpb/Uw**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6wFk4GhDJaFpA+dj6x9nY+seQ**dAQDAA**AAMAAA**aVhA51caYktb6PXO3jBouYaVfRDU0NtshjlovBNmlr8va+flnTwhc810vYE4CnesvHAmA80QWq2CeyxjLgkwiVQr2J7mzQrs/xJ6rLbGhyZcjspoHHu3/w5Ug+KPSqwFVOMHizsXczKJr9ychecuwL4OgZkQsPHb80xEJ9t5bkKXm7dGJow3ltFswoSg+8aUUO+JQeDqPuhHhpBv7uWJH+R+fVnGFu4v+oh1lWBbrqm7mrtuiKdKxWX/gghkBBxIV1Pb8Y4UqqcUYXs0sphxOeIcymA+WUG70dC0u0/McUW+oXD/mp6c+8et+OPs7HPEP5QoH1Jl9OAJQ2OZ5f04bIW+W64ErKSJg6a68IpS3weJAYAI7wg90lePB99AHyWA6CVgdSV4f5qOgOu6ZVyzjdeeHbpxawa+GJjHBjrRSZhKytm1e9/4vNSSH+7q1UyWT78UvQ+6uNNlSc71TBidPOeDCmgH83TOk0ALcgDX2ALLvkqaQuKlu3IJu6EJmRvJ/4jhVeaaqdFsz9EFGGEYtgqbz71+3lnE9072NtKGKxoHsQNRxwCtLeSEaAFWjm2/DLBeubs5wnpWrM9+5IOiZpYJH6Hn1VAkWf3QbsA2NDNfYOp/UNreTx3Zz3GjQ/CG1HxVChlB3/nJLMxYQTq/Io4/ri7gEC2DzfiBb+uMh3VIT1xRzAwt6z5y7RPjefe4tVLInQctAwFvS64u065yRw5+AZ3kmL9EjPQGeJhanf7NmqekJDJUX1UdmeWR6/pU';
    }

}
?>