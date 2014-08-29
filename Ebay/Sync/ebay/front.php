<?php

        //SiteID must also be set in the Request's XML
        //SiteID = 0  (US) - UK = 3, Canada = 2, Australia = 15, ....
        //SiteID Indicates the eBay site to associate the call with
        $siteID = 0;
        //the call being made:
        $verb = 'GetSessionID';

        ///Build the request Xml string
        $requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';
        $requestXmlBody .= '<GetSessionIDRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        $requestXmlBody .= '<RuName>'.$ebay->runame.'</RuName>';
        $requestXmlBody .= '</GetSessionIDRequest>';
		//echo "__ : " .$ebay->runame."///";
        //Create a new eBay session with all details pulled in from included keys.php
        $session = new eBaySession($verb,$ebay);

        //send the request and get response
        $responseXml = $session->sendHttpRequest($requestXmlBody);
        if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
            die('<P>Error sending request');

        //Xml string is parsed and creates a DOM Document object
        $responseDoc = new DomDocument();
        $responseDoc->loadXML($responseXml);


        //get any error nodes
        $errors = $responseDoc->getElementsByTagName('Errors');

        //if there are error nodes
        if($errors->length > 0)
        {
            echo '<P><B>eBay returned the following error(s):</B>';
            //display each error
            //Get error code, ShortMesaage and LongMessage
            $code = $errors->item(0)->getElementsByTagName('ErrorCode');
            $shortMsg = $errors->item(0)->getElementsByTagName('ShortMessage');
            $longMsg = $errors->item(0)->getElementsByTagName('LongMessage');
            //Display code and shortmessage
            echo '<P>', $code->item(0)->nodeValue, ' : ', str_replace(">", "&gt;", str_replace("<", "&lt;", $shortMsg->item(0)->nodeValue));
            //if there is a long message (ie ErrorLevel=1), display it
            if(count($longMsg) > 0)
                echo '<BR>', str_replace(">", "&gt;", str_replace("<", "&lt;", $longMsg->item(0)->nodeValue));

        }

        else //no errors
        {
            //get the nodes needed
            $sessionIDNode = $responseDoc->getElementsByTagName('SessionID');
            //Display the details
            $sessionID = $sessionIDNode->item(0)->nodeValue;
            $_SESSION['sesId'] = $sessionID;

        }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<HTML>
<HEAD>
<META http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<TITLE>Get eBay User Items</TITLE>
</HEAD>
<BODY>
<FORM action="" method="post">
    <h2>Testing eBay Connection Plugin</h2>
    <h3>Linking User Account to our website</h3>
    <p>Session ID: <?php echo $_SESSION['sesId']; ?></p>
    <BR><a target="_new" href="https://signin.ebay.com/ws/eBayISAPI.dll?SignIn&RuName=<?php echo $RuName; ?>&SessID=<?php echo $sessionID; ?>">Click Here To Link Your Ebay Account To Our Website</a>
</FORM>
</BODY>
</HTML>