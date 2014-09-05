<?php
class Ebay_Sync_Block_Monblock extends Mage_Core_Block_Template
{
     public function methodblock()
     {
         global $appID,$devID,$certID,$RuName,$serverUrl, $userToken,$compatabilityLevel, $siteID;
		  initKeys();
		  // 
		  session_start();
		  $ebay = new Ebay($appID,$devID,$certID,$RuName,$serverUrl, $userToken,$compatabilityLevel, $siteID);
		  if( $ebay->getStuff() ){
			  $_SESSION['itemIds'] = $ebay->itemIds;			   
		  }
     }
}


/*
			  $i = 0;
			  foreach($ebay->itemIds as $itemid ){
				   $item = new Item();
					 $item = $ebay->getItemData($itemid);
					 if( $item->sku == "" || !isset($item->sku) ){
						echo "Item with EbayId:".$itemid." has no SKU so not imported!<br>"; continue;
					 }
					 $cat = $item->checkCreateCategoryTree();	
					 $item->checkCreateProduct($cat);
					 $item->downloadImages();
				  $i++;
			  }
			  echo $i." Products imported! ";

*/

?>