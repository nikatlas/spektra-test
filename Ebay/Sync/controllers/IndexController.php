<?
require_once ( Mage::getBaseDir('code').'/local/Ebay/Sync/ebay/eBay.php' );
include_once( Mage::getBaseDir('code').'local/Ebay/Sync/ebay/keys.php' );


function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
class Ebay_Sync_IndexController extends Mage_Core_Controller_Front_Action
{
   public function indexAction ()
   {
		if( !$this->checkActivityProductAttribute() ){
			 $this->createActivityProductAttributeAction();
			 echo " [!] -> Refresh ! " ;
			 return;
		}
		if( !$this->checkActivityAttribute() ){
			 $this->createActivityAttributeAction();
			 echo " [!] -> Refresh ! " ;
			 return;
		}
		$this->loadLayout();
		$this->renderLayout();
		return;
   }
   public function getNewAction(){
	   if( !$this->checkActivityProductAttribute() ){
			 $this->createActivityProductAttributeAction();
			 echo " [!] -> Refresh ! " ;
			 return;
		}
		if( !$this->checkActivityAttribute() ){
			 $this->createActivityAttributeAction();
			 echo " [!] -> Refresh ! " ;
			 return;
		}
		$this->loadLayout();
		$this->renderLayout();
   }
   public function cronAction(){
		global $appID,$devID,$certID,$RuName,$serverUrl, $userToken,$compatabilityLevel, $siteID;
	    initKeys();
		  $_REQUEST['time_from_get'] = 1;
		  session_start();
		  $ebay = new Ebay($appID,$devID,$certID,$RuName,$serverUrl, $userToken,$compatabilityLevel, $siteID);

		 if( $ebay->getNewStuff() ){
			  $_SESSION['newItemIds'] = $ebay->itemIds;			   
			  echo sizeof($_SESSION['newItemIds'])."/<br>";
			  foreach($ebay->itemIds as $item){
				  $this->itemAction($item);
			  }
		  }
		  else{
			exit("-1");  
		  }   
   }
   
   public function checkActivityAttribute(){
		require_once "app/Mage.php";
		
		Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));
	
		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		$query = "SELECT count(*) AS `max` FROM eav_attribute WHERE attribute_code='activity'";
		$results = $readConnection->fetchAll($query);
		if( $results[0]['max'] > 0 ){
			return true;
		}   
		return false;
   }
   public function createActivityAttributeAction () {
    require_once "app/Mage.php";

    Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));

	$resource = Mage::getSingleton('core/resource');
    $readConnection = $resource->getConnection('core_read');
    $query = "SELECT count(*) AS `max` FROM eav_attribute WHERE attribute_code='activity'";
    $results = $readConnection->fetchAll($query);
	if( $results[0]['max'] > 0 ){
		echo ("[!] The attribute is already installed!");	
		return;
	}
	
    $installer = new Mage_Sales_Model_Mysql4_Setup;
	
	$installer->startSetup();
    // change details below:
    $attribute  = array(
        'type' => 'int',
        'label'=> 'Activity stability',
        'input' => 'text',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible' => true,
        'required' => false,
        'user_defined' => true,
        'default' => "1",
        'group' => "General Information"
    );

    $installer->addAttribute('catalog_category', 'activity', $attribute);

    $installer->endSetup();   
	echo "[*] Adding Activity attribute to existing nodes...  <br>";
	
	$categories = Mage::getModel('catalog/category')->getCollection()->load();
	foreach($categories as $cat)$cat->save();
	echo "[*] Installed";	
   }
   public function checkActivityProductAttribute(){
		require_once "app/Mage.php";
		
		Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));
	
		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		$query = "SELECT count(*) AS `max` FROM eav_attribute WHERE attribute_code='activity_product'";
		$results = $readConnection->fetchAll($query);
		if( $results[0]['max'] > 0 ){
			return true;
		}   
		return false;
   }
   public function createActivityProductAttributeAction () {
		require_once "app/Mage.php";
	
		Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));
	
		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		$query = "SELECT count(*) AS `max` FROM eav_attribute WHERE attribute_code='activity_product'";
		$results = $readConnection->fetchAll($query);
		if( $results[0]['max'] > 0 ){
			echo ("[!] The attribute is already installed!");	
			return;
		}
		
		$installer = new Mage_Sales_Model_Mysql4_Setup;
		
		$installer->startSetup();
		// change details below:
		$attribute  = array(
			'type' => 'int',
			'label'=> 'Activity stability',
			'input' => 'text',
			'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
			'visible' => true,
			'required' => false,
			'user_defined' => true,
			'default' => "1",
			'group' => "General"
		);
	
		$installer->addAttribute('catalog_product', 'activity_product', $attribute);
	
		$installer->endSetup();   
		echo "[*] Adding ActivityProduct attribute to existing nodes...  <br>";
		
		$categories = Mage::getModel('catalog/product')->getCollection()->load();
		foreach($categories as $cat)$cat->save();
		echo "[*] Installed";	
   }
   public function disableCategoriesAction (){
	$resource = Mage::getSingleton('core/resource');
    $readConnection = $resource->getConnection('core_read');
    $query = "SELECT attribute_id FROM eav_attribute WHERE attribute_code='activity'";
    $results = $readConnection->fetchAll($query);
	if( sizeof( $results) == 0 ) {
		echo "Activity attribute doesnt exist!";return;	
	}	
    $resource = Mage::getSingleton('core/resource');
    $writeConnection = $resource->getConnection('core_write');
    $query = "UPDATE  catalog_category_entity_int SET value='0' WHERE attribute_id=".$results[0]['attribute_id'];
    $writeConnection->query($query);
	echo "0";
   }
   public function enableCategoriesAction (){
	$resource = Mage::getSingleton('core/resource');
    $readConnection = $resource->getConnection('core_read');
    $query = "SELECT attribute_id FROM eav_attribute WHERE attribute_code='activity'";
    $results = $readConnection->fetchAll($query);
	if( sizeof( $results) == 0 ) {
		echo "Activity attribute doesnt exist!";return;	
	}
    $resource = Mage::getSingleton('core/resource');
    $writeConnection = $resource->getConnection('core_write');
    $query = "UPDATE  catalog_category_entity_int SET value='1' WHERE attribute_id=".$results[0]['attribute_id'];
    $writeConnection->query($query);
	echo "0";
   }
   public function disableAction (){
	$resource = Mage::getSingleton('core/resource');
    $readConnection = $resource->getConnection('core_read');
    $query = "SELECT attribute_id FROM eav_attribute WHERE attribute_code='activity_product'";
    $results = $readConnection->fetchAll($query);
	if( sizeof( $results) == 0 ) {
		echo "Activity attribute doesnt exist!";return;	
	}
	
    $resource = Mage::getSingleton('core/resource');
    $writeConnection = $resource->getConnection('core_write');
    $query = "UPDATE catalog_product_entity_int SET value='2' WHERE attribute_id=".$results[0]['attribute_id'];
    $writeConnection->query($query);
	echo "0";
   }
   public function enableAction (){
	$resource = Mage::getSingleton('core/resource');
    $readConnection = $resource->getConnection('core_read');
    $query = "SELECT attribute_id FROM eav_attribute WHERE attribute_code='activity_product'";
    $results = $readConnection->fetchAll($query);
	if( sizeof( $results) == 0 ) {
		echo "Activity attribute doesnt exist!";return;	
	}
	
	$resource = Mage::getSingleton('core/resource');
    $writeConnection = $resource->getConnection('core_write');
    $query = "UPDATE catalog_product_entity_int SET value='1' WHERE attribute_id=".$results[0]['attribute_id'];
    $writeConnection->query($query);
	echo "0";
   }
   
   public function deleteInactiveCategoriesAction(){
	    Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));
   		$categories = Mage::getModel('catalog/category')->getCollection()
						->addAttributeToFilter('activity', 0)
						->addAttributeToFilter('level' , array( 'gt' => 1 ) );
		$categories->load();
		foreach( $categories as $cat ) {
			//echo " <BR> [!] NAME : " . $cat->getName() . " !! " ;	
			$cat->delete();
		}
		echo "0";
   }
   public function fixTaxAction(){
	    	$resource = Mage::getSingleton('core/resource');
			$readConnection = $resource->getConnection('core_read');
			$query = "SELECT attribute_id FROM eav_attribute WHERE attribute_code='tax_class_id'";
			$results = $readConnection->fetchAll($query);
			if( sizeof( $results) == 0 ) {
				echo "Activity attribute doesnt exist!";return;	
			}
			
			$resource = Mage::getSingleton('core/resource');
			$writeConnection = $resource->getConnection('core_write');
			$query = "UPDATE catalog_product_entity_int SET value='0' WHERE attribute_id=".$results[0]['attribute_id'];
			$writeConnection->query($query);
			echo "0";
   }
   public function deleteInactiveAction(){
	    Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));
   		$categories = Mage::getModel('catalog/product')->getCollection()
						->addAttributeToFilter('activity_product', 2);
		$categories->load();
		foreach( $categories as $cat ) {
			$cat->delete();
		}
		echo "0";
   }

   public function storeAction(){
	   echo "!";
	   global $appID,$devID,$certID,$RuName,$serverUrl, $userToken,$compatabilityLevel, $siteID;
		 initKeys();
		 session_start();
	   	 $ebay = new Ebay($appID,$devID,$certID,$RuName,$serverUrl, $userToken,$compatabilityLevel, $siteID);
		 $xml = $ebay->GetStoreCategories(); 
		 echo $xml ; 
		 
		 $doc = new DOMDocument();
		 $doc->loadXML($xml);
		 
		 //REQ ID
		 $reqID = 5996711015;
		 ///////
		 echo "<BR><BR>HERE:<BR>";
		 
		 $chcat = $doc->getElementsByTagName("ChildCategory");
		 foreach( $chcat as $cat ){
			 if( $cat->getElementsByTagName("CategoryID")->item(0)->nodeValue  == $reqID ){
				$name = $cat->getElementsByTagName("Name")->item(0)->nodeValue;
				$par=$cat->parentNode;
				while ( $par->nodeName == "ChildCategory" ){ 
					$name = $par->getElementsByTagName("Name")->item(0)->nodeValue.":".$name;	
					$par=$par->parentNode;
				}
				$name = $par->getElementsByTagName("Name")->item(0)->nodeValue.":".$name;	
				break; 
			 }
		 }
		 echo $name;

   }
   public function itemAction ($itemid = '', $sale = -4, $store = 1)
   {	 
   	 $itemid = isset($_REQUEST['itemid']) ? $_REQUEST['itemid'] : $itemid;
	 if ( !isset($itemid) || $itemid == "" ) {
				 echo "-2";return; 
	 }

	 $sale = isset($_REQUEST['sale']) ? $_REQUEST['sale'] :$sale ;
	 $store = isset($_REQUEST['store']) ? $_REQUEST['store'] :$store;
	 
	 global $appID,$devID,$certID,$RuName,$serverUrl, $userToken,$compatabilityLevel, $siteID;
	 initKeys();
	 session_start();
	 $ebay = new Ebay($appID,$devID,$certID,$RuName,$serverUrl, $userToken,$compatabilityLevel, $siteID);
	 $item = new Item();
	 $item = $ebay->getItemData($itemid);     
	 if ( !isset($item->sku) || $item->sku == "" ) {
		 $item->sku = $item->itemid;
		 //echo "-1";return; 
	 }
	 Mage::app()->setCurrentStore($store);
	 $cat = $item->checkCreateCategoryTree($item->categoryName,$store);		
	 $item->checkCreateProduct($cat,$sale,$store);
	 $item->downloadImages();	
	 echo "0&".$item->sku;
	 return;	
   }
   public function itemdebugAction ()
   {
	 $t = microtime(true);
	 $itemid = $_REQUEST['itemid'];
	 $sale = 0;
	 if ( !isset($_REQUEST['itemid']) || $itemid == "" ) {
				 echo "-2";return; 
	 }
	 global $appID,$devID,$certID,$RuName,$serverUrl, $userToken,$compatabilityLevel, $siteID;
	 initKeys();
	 session_start();
	 $ebay = new Ebay($appID,$devID,$certID,$RuName,$serverUrl, $userToken,$compatabilityLevel, $siteID);
	 $item = new Item();
	 
	 echo "INIT:".(microtime(true) - $t)."<br>";
	 $t = microtime(true);
	 
	 $item = $ebay->getItemData($itemid); 
	 var_dump($item);    
	 if ( !isset($item->sku) || $item->sku == "" ) {
				 echo "-1";return; 
	 }
	 
	 echo "GET:".(microtime(true) - $t)."<br>";
	 $t = microtime(true);
	 
	 $cat = $item->checkCreateCategoryTree($item->categoryName);		
	 
	 echo "CATS:".(microtime(true) - $t)."<br>";
	 $t = microtime(true);
	 
	 $item->checkCreateProduct($cat,$sale);
	 
	 echo "Prod:".(microtime(true) - $t)."<br>";
	 $t = microtime(true);
	 
	 $item->downloadImages();
	 
	 echo "IMG:".(microtime(true) - $t)."<br>";
	 $t = microtime(true);
	 
		
	 //echo "0";
	 return;	
   }
   
   public function mamethodeAction ()
   {
	 // GLOBALS INITIATED IN KEYS
	 global $appID,$devID,$certID,$RuName,$serverUrl, $userToken,$compatabilityLevel, $siteID;
	 initKeys();


     // 
	 session_start();
	 $ebay = new Ebay($appID,$devID,$certID,$RuName,$serverUrl, $userToken,$compatabilityLevel, $siteID);
	 $ebay->getStuff();
	 $item = new Item();
	 $item = $ebay->getItemData(221455735202);     
	 $cat = $this->checkCreateCategoryTree($item->categoryName);	 
	 $this->checkCreateProduct($cat);
	 $this->downloadImages();
	
   }
   public function testAction (){
	  
		
	   Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
		
		$pid = Mage::getModel('catalog/product')->getIdBySku("xx04852");
		$product = Mage::getModel('catalog/product')->load($pid);
		
		$product->setName("NAME :" . rand());
 	    $time_start = microtime(true);
		$product->save();
		$time = microtime(true) - $time_start;
		echo "TIME : " .$time;
   }
   
   public function testtestAction(){	
     //   $attr = Mage::getModel('catalog/product')->getAttributes();
		//var_dump($attr);
        $prod = Mage::getModel('catalog/product')
			->setAttributeSetId(4)->loadByAttribute('sku','xx00146');
	  	var_dump($prod);
		echo "!".$prod->getData("news_from_date"),"!".$prod->getData("sku");
   }
   public function syncActiveListAction (){	   
	 // GLOBALS INITIATED IN KEYS
	 global $appID,$devID,$certID,$RuName,$serverUrl, $userToken,$compatabilityLevel, $siteID;
	 initKeys();
     // 
	 session_start();
	 $ebay = new Ebay($appID,$devID,$certID,$RuName,$serverUrl, $userToken,$compatabilityLevel, $siteID);
	 $ebay->getActiveStuff();
	
	
	 $this->disableAction(); // DISABLE 
	 $time = microtime(true);

	 $doubles = $this->getDoublesAndRemoveInArray($ebay->skus);
	 
	 $missing = 0;
	 $activated = 0;
	 $ssskus = array();
	 
	 
		
	 foreach ( $ebay->skus as $sku ){ // UPDATE 
        $prod = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);
		if( !$prod ){
			array_push($ssskus , $sku );
			$missing++;			
		}else{
			$prod->setData('activity_product' , 1);
			$prod->getResource()->saveAttribute($prod, 'activity_product'); 
			$activated++;
		}
	 }
	 $dt = microtime(true) - $time;
	 
	 $this->deleteInactiveAction(); // DELETE 
	 
	 //print_r($ssskus);
	 echo "Size :" . sizeof($ebay->skus) . "<BR>";
	 echo "Activated :" . $activated . "<BR>";
	 echo "Missing:" . $missing . "<BR>";
	 echo "Time needed:" . $dt. "<BR>";
	 echo "<BR> Doubles :".sizeof( $doubles ) . " <BR> " .implode("," , $doubles)."<BR>";
	 
   }
   public function 	 getDoublesAndRemoveInArray( &$arr){
		$h = array_count_values ( $arr );
		$rem = array();
		foreach( $h as $key=>$freq ){
			if( $freq > 1 ){
				$remove = array($key);
				$arr = array_diff($arr, $remove); 					
				array_push($rem , $key );
				array_push($arr , $key );
			}
		}
		return $rem;
   }
   public function checkSku($sku){
			$id = Mage::getModel('catalog/product')->getIdBySku($sku);
			if ($id){
				return true;
			}
			else{
				return false;
			}   
   }
}

class Item {
	function __construct(){
	return;	
	}
	public function setSku($sku){
		$this->sku = $sku;	
	}	
	
   public function checkSku($sku){
			$id = Mage::getModel('catalog/product')->getIdBySku($sku);
			if ($id){
				return true; $resource = Mage::getSingleton('core/resource');
    $writeConnection = $resource->getConnection('core_write');
    $query = "UPDATE  catalog_product_entity_int SET value=2 WHERE attribute_id=96";
    $writeConnection->query($query);
			}
			else{
				return false;
			}   
   }
   // CATEGORY STUFF
   public function clearEbayTag($name){
	   $r = explode(' ',$name);
	   for( $i=0;$i<sizeof($r);$i++ ){
	   		if( strlen( $r[$i] ) > 12 )
				$r[$i] = str_replace('/' , '/ ' , $r[$i]); 
	   }
	   $name = implode(" " , $r);
		return str_replace('ebay','', str_replace('Ebay','',str_replace('eBay','' , $name)));
   }
   // REMEMBER CAT NAMES ARE TRIMMED FOR EBAY TAG
   public function checkCategoryByName($name, $parent){
	   		$name = $this->clearEbayTag($name);
//			$id = Mage::getModel('catalog/category')->loadByAttribute('name',$name); 
			$id = Mage::getModel('catalog/category')->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('name', array('like'=>$name))->load(); // TODO

			foreach ( $id as $idd ){				
			   if( is_integer($parent) ){
					if( $idd->getParentCategory()->getId() == $parent ){
						if( $idd->getData('activity') == 0 ){
							$idd->setData('activity',1);
							$idd->save();	
						}
						return $idd;   
					}
			   }
			   else{
				   if( $idd->getParentCategory()->getName() == $parent || $parent == Mage::app()->getStore($store)->getRootCategoryId())
						if( $idd->getData('activity') == 0 ){
							$idd->setData('activity',1);
							$idd->save();
						}
					   return $idd; 
			   }
			}

			return false;
   }
   // REMEMBER CAT NAMES ARE TRIMMED FOR EBAY TAG
   public function createCategoryByName($name,$parent,$store){
  	   $name = $this->clearEbayTag($name);
   	   $parent = (is_integer($parent))? $parent : $this->clearEbayTag($parent);
	   $t = $this->checkCategoryByName($name, $parent);
	   if( $t ){
		   return $t;
	   }
	   try{
			$category = Mage::getModel('catalog/category');
			$category->setStoreId($store);
			$category->setName($name);
			$category->setUrlKey($name);
			$category->setIsActive(1);
			$category->setDisplayMode('PRODUCTS');
			$category->setIsAnchor(1); //for active achor
			$category->setData('activity'  , 1);
			if( is_integer($parent) ){
				$parentCategory = Mage::getModel('catalog/category')->load(intval($parent));	
			}else{
				$parentCategory = Mage::getModel('catalog/category')->loadByAttribute('name',$parent);
			}
			if( $parentCategory ) {
				$category->setPath($parentCategory->getPath());
			}
			else{
				$category->setPath('1/'.$parent);
			}
			$category->save();
		} catch(Exception $e) {
			var_dump($e);
		}
		return $category;
   }
   public function checkCreateCategoryTree($catName,$store){
	   $cats = explode(':',$catName);
	   $t = $this->createCategoryByName($cats[0], Mage::app()->getStore($store)->getRootCategoryId() , $store);
	   for( $i=1;$i<sizeof($cats);$i++){
	   	   $t = $this->createCategoryByName($cats[$i],$cats[$i-1] , $store);
	   } 
	   return $t;
   }
   // REMEMBER TO FIX RATES CURRENCY
   public function priceToEur($price,$currency){
   		$from = $currency;
		$to = Mage::app()->getStore()->getCurrentCurrencyCode();
		$allowedCurrencies = Mage::getModel('directory/currency')->getConfigAllowCurrencies();   
		$currencyRates = Mage::getModel('directory/currency')->getCurrencyRates('EUR', array_values($allowedCurrencies));
		$rate = 1/$currencyRates[$from];
		$newPrice = $price*$rate;
		return $newPrice;
   }
   public function checkCreateProduct($cat,$sale,$store){
	   
	   try{
	   	Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
		if( $this->checkSku($this->sku) == false ){
			$product = Mage::getModel('catalog/product');

			$product->setAttributeSetId(4); 
			$product->setSku($this->sku);	
			$product->save();
			echo "1";
		}
		else{
			echo "2";	
		}
		$pid = Mage::getModel('catalog/product')->getIdBySku($this->sku);
		$product = Mage::getModel('catalog/product')->load($pid);

		$product->setAttributeSetId(4); 
		$product->setSku($this->sku);
		$product->setName($this->name);
		$product->setDescription($this->description);
		$product->setShortDescription("&nbsp;");
		$product->setPrice($this->priceToEur($this->price,$this->currency)*((100+$sale)/100));
		//$product->setTypeId('simple');
		$product->setCategoryIds(array($cat->getId())); 
		$product->setWeight(isset($this->weight)?$this->weight:0.0); // need to fetch weight , I am not currently
		$product->setTaxClassId(0); // taxable goods
		$product->setVisibility(4); // catalog, search
		$product->setStatus(1); // enabled
		$product->setData('activity_product' , 1);
		//$product->setPromotion(0); // enabled
		$product->setDesignstyle(11); // enabled
		// assign product to the default website
		$product->setWebsiteIds(array($store));	
		$product->setMetaTitle($this->name);
		
 		$date = new DateTime();
		$date->add(new DateInterval('P2D'));
		$datef = new DateTime();
		$datef->sub(new DateInterval('P2D'));
		
		$product->setData('news_to_date' , $date->format("Y-m-d H:i:s"));
		$product->setData('news_from_date' , $datef->format("Y-m-d H:i:s"));
		
		// for stock
		$stockData = $product->getStockData();
		$stockData['qty'] = $this->quantity;
		$stockData['is_in_stock'] = (($this->quantity>0)?1:0);
		$stockData['manage_stock'] = 1;
		$stockData['use_config_manage_stock'] = 0;
		$product->setStockData($stockData);
		
		$product->save();	
	   } catch(Exception $e) {
			var_dump($e);
	   }
   }
   public function checkIfImageExists($product, $pic){
  	    foreach ($product->getMediaGalleryImages() as $image) {
				if( strpos( $image->getUrl() , $pic )!=false  )return true;
		} 
		return	 false;
   }
   public function downloadImages(){
	   //try{
       $pid = Mage::getModel('catalog/product')->getIdBySku($this->sku);
	   $product = Mage::getModel('catalog/product')->load($pid); 

	   for( $i=sizeof($this->pictures)-1;$i>=0;$i--){
			$pic = 		   $this->pictures[$i];
			$image_url  = $pic; //get external image url from csv
			$image_type = substr(strrchr($image_url,"."),1,3); //find the image extension
			$filename   = md5($image_url . $this->sku).'.'.$image_type; //give a new name, you can modify as per your requirement
			$filepath   = Mage::getBaseDir('media') . DS . 'downloadable'. DS . $filename; //path for temp storage folder: ./media/import/
			/// CHECK DONT REDOWNLOAD!!
			if( $this->checkIfImageExists($product , md5($image_url . $this->sku) ) )continue;
			file_put_contents($filepath, file_get_contents(trim($image_url))); //store the image from external url to the temp storage folder
			$mediaAttribute = array (
					'thumbnail',
					'small_image',
					'image'
			);
			$product->addImageToMediaGallery($filepath, $mediaAttribute, false, false);
	   }
	   $product->save();
	   //}
	   //catch(Exception $e ){
			//var_dump($e);
	   //}
   }
}
?>