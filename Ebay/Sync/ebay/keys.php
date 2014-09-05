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
        $devID = 'f2a7f4ed-c06f-494d-bc2e-af34346a2705';   // these prod keys are different from sandbox keys
        $appID = 'SpektraM-a390-4335-825a-25770418f312';
        $certID = '3de3717a-9916-44c2-9af2-e7ee936d7e65';
        $RuName = 'SpektraMaxima-SpektraM-a390-4-ydzzsn';
        //set the Server to use (Sandbox or Production)
        $serverUrl = 'https://api.ebay.com/ws/api.dll';      // server URL different for prod and sandbox
        //the token representing the eBay user to assign the call with
        $userToken = 'AgAAAA**AQAAAA**aAAAAA**YrIFVA**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6wAk4ukDJiCqQudj6x9nY+seQ**Y3UCAA**AAMAAA**yv0tNXwj6HOQrj78CTnHTwz2wzkS6MfWJskbGTR94xYAul7XYDJOeD3cSFDurZrEmBmI4dLYPD8OVzeyQ1+CqKuWTDVp5l9hETPyrDErUh16VA7UteeADnm88LPX/O9auby9paxB/tE4IOuj53ypXk4YARl/FPIQrNISqCzK8rlOc6nLznO28lb9iELJvm2K9DThJPFRQHMpuIzd+GbEWX1h8VOLH27ANy6G6CEBe+3SWEvMK8d+0MQOvmuDL6xffOrXwybxQa74niXS+HY1wtrfk4GTvn8DL5a4FKAyjPY0AKu6BNywLJVk6b09zjCml4SXnvg1Zn2ceLDnMWe2riu512CkesW6u4C7kyEcwP6wm9fyTaq0WXsWLMnBFoTrFGXnRq9s56q7KAmLWwkOkpGOqtDkds1YuQM931OYGM3WP/nlzHgtyPSApfWbzoccLDv0PW4jDxVBN3E8W7rZZQbHg+bSGzPfesNTxo+96FT7yjnFq92UGR6gdp4/NmgsoaAmYplWLMRN7eQTUJoIBBIOZZ3wRjaXVg+CkDCChy8VCdoQpaEVNA22YxRuMpIMAQpHDmeMTvQsNvEqqHMsbTfdgOUuA/kMBjYJCcsyK5zuhiIsAWJH/T1ZCrH4bd/2OPB4+uIpk4RRWrzUru8XlFXrtRFX1Dass9PPByDt8oUjyX55OVrZN9UYnuysa2UbdWjdXwgqxKxQMxlY6ER0BdOCv+/f6dtiUKYWvONAvUNoI4xDzQJ88fZ9g5CvBysR';
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