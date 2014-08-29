<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<HTML>
<HEAD>
<META http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<TITLE>Get eBay User Items (Result)</TITLE>
</HEAD>
<BODY>
    <h2>Testing eBay Connection Plugin</h2>
    <h3>Receiving User Tocken</h3>
    <h4>With a User Tocken ID we can import user data to our website.</h4>

    <?php

			/*
            //SiteID must also be set in the Request's XML
            //SiteID = 0  (US) - UK = 3, Canada = 2, Australia = 15, ....
            //SiteID Indicates the eBay site to associate the call with
            $siteID = 0;
            //the call being made:
            $verb = 'FetchToken';

            ///Build the request Xml string
            $requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';
            $requestXmlBody .= '<FetchTokenRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
            $requestXmlBody .= '<SessionID>'.$_SESSION["sesId"].'</SessionID>';
            $requestXmlBody .= '</FetchTokenRequest>';

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
                echo '<BR/>User Session ID: '.$_COOKIE["sesId"].'';
                if(count($longMsg) > 0)
                    echo '<BR>', str_replace(">", "&gt;", str_replace("<", "&lt;", $longMsg->item(0)->nodeValue));

            }

            else //no errors
            {
                //get the nodes needed
                $eBayAuthTokenNode = $responseDoc->getElementsByTagName('eBayAuthToken');

                //Display the details
                echo '<BR/>User Session ID: '.$_SESSION["sesId"].'';
                echo '<BR/><BR/>User Token: '.$eBayAuthTokenNode->item(0)->nodeValue.'';

            }
			//*/
			$res =  $ebay->ebayManagement();
			$doc = new DOMDocument();
			$doc->loadXML($res['myeBaySellingXml']);
			$doc->save("tempXML.xml");
			//	echo $res['myeBaySellingXml'];
			$items = $doc->getElementsByTagName("ItemID");
			$ebayItems = array();
			foreach( $items as $item ){
				echo $item->nodeName.":".$item->nodeValue."<BR>";	
				array_push($ebayItems, $item->nodeValue);
			}
			echo "I have Item IDs <br>";
			
    ?>

    </BODY>
    </HTML>