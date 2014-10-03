<?php
class Ebay_Sync_Block_Newblock extends Mage_Core_Block_Template
{
     public function methodblock()
     {
         global $appID,$devID,$certID,$RuName,$serverUrl, $userToken,$compatabilityLevel, $siteID;
		  initKeys();
		  // 
		  session_start();
		  $ebay = new Ebay($appID,$devID,$certID,$RuName,$serverUrl, $userToken,$compatabilityLevel, $siteID);

		 if( $ebay->getNewStuff() ){
			  $_SESSION['newItemIds'] = $ebay->itemIds;			   
			  //echo sizeof($_SESSION['itemIds']);
		  }
		  else{
			exit("ERROR ON GET STUFF!");  
		  }
     }
}
?>