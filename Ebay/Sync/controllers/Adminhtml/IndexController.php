<?
require_once ( Mage::getBaseDir('code').'/local/Ebay/Sync/ebay/eBay.php' );
include_once( Mage::getBaseDir('code').'local/Ebay/Sync/ebay/keys.php' );

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
class Ebay_Sync_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
   public function indexAction ()
   {
		$this->loadLayout();
		$this->renderLayout();
		return;
	   //exit("ads");
	   global $appID,$devID,$certID,$RuName,$serverUrl, $userToken,$compatabilityLevel, $siteID;
		 initKeys();
		 // 
		 session_start();
		 $ebay = new Ebay($appID,$devID,$certID,$RuName,$serverUrl, $userToken,$compatabilityLevel, $siteID);
		 $ebay->getStuff();
		 //echo sizeof($ebay->itemIds);
		 //$xml = $ebay->getItem($ebay->itemIds[0]);
 		 $xml = $ebay->getItem(221455735202);
		 $doc = new DOMDocument();
		 $doc->loadXML($xml);
		
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
		
		$pid = Mage::getModel('catalog/product')->getIdBySku(221490380408);
		$new_product = Mage::getModel('catalog/product')->load($pid);
		//$new_product->load($pid);
	 	$new_product->setSku(221490380408);
		
		$new_product->setCategoryIds(array(1152));
		//$new_product->setAttributeSetId(4); 
		$new_product->setCurrency("GBP"); 
		$new_product->setVisibility(4);             
		$new_product->setQuantity(10);             
		$new_product->setManageStock(1);             
		$new_product->setTypeId('simple');
		$new_product->setName($doc->getElementsByTagName("Title")->item(0)->nodeValue);
		$new_product->setDescription('test');
		//$new_product->setShortDescription('test');
		$new_product->setStatus(2); 
		$new_product->setTaxClassId(4);
		$new_product->setWeight('');             
		//$new_product->setCreatedAt(strtotime('now'));                   
	
		$new_product->setPrice(10.42);
		$new_product->setCost(11.42);
		$new_product->setSpecialPrice(12.42);
		$new_product->setWebsiteIds(array(Mage::app()->getStore(true)->getWebsite()->getId()));	

		$new_product->save();

		
		/*$visibility = array (
		  'thumbnail',
		  'small_image',
		  'image'
		  );
		$filePath =  Mage::getBaseDir('media') . '/downloadable/test.jpg';
		$new_product->addImageToMediaGallery($filePath,$visibility,true,false);
		// call save() method to save your product with updated data
	 	*/

     echo 'test index';
   }
   
   public function disableAction (){
    $resource = Mage::getSingleton('core/resource');
    $writeConnection = $resource->getConnection('core_write');
    $query = "UPDATE  catalog_product_entity_int SET value='2' WHERE attribute_id=96";
    $writeConnection->query($query);
   }
   public function enableAction (){
	$resource = Mage::getSingleton('core/resource');
    $writeConnection = $resource->getConnection('core_write');
    $query = "UPDATE catalog_product_entity_int SET value='1' WHERE attribute_id=96";
    $writeConnection->query($query);
   }
   
   public function itemAction ()
   {
	 $itemid = $_REQUEST['itemid'];
	 $sale = $_REQUEST['sale'];
	 if ( !isset($_REQUEST['itemid']) || $itemid == "" ) {
				 echo "-2";return; 
	 }
	 global $appID,$devID,$certID,$RuName,$serverUrl, $userToken,$compatabilityLevel, $siteID;
	 initKeys();
	 session_start();
	 $ebay = new Ebay($appID,$devID,$certID,$RuName,$serverUrl, $userToken,$compatabilityLevel, $siteID);
	 $item = new Item();
	 $item = $ebay->getItemData($itemid);     
	 if ( !isset($item->sku) || $item->sku == "" ) {
				 echo "-1";return; 
	 }
	 $cat = $item->checkCreateCategoryTree($item->categoryName);		
	 $item->checkCreateProduct($cat,$sale);
	 $item->downloadImages();
		
	 echo "0";
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
		
		$pid = Mage::getModel('catalog/product')->getIdBySku("test");
		$product = Mage::getModel('catalog/product')->load($pid);
		
		$product->setName("NAME :" . rand());
 	    $time_start = microtime(true);
		$product->save();
		$time = microtime(true) - $time_start;
		echo "TIME : " .$time;
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
		return str_replace('ebay','', str_replace('Ebay','',str_replace('eBay','' , $name)));
   }
   // REMEMBER CAT NAMES ARE TRIMMED FOR EBAY TAG
   public function checkCategoryByName($name){
	   		$name = $this->clearEbayTag($name);
			$id = Mage::getModel('catalog/category')->loadByAttribute('name',$name); 
			if ($id){
				return $id;
			}
			else{
				return false;
			}   
   }
   // REMEMBER CAT NAMES ARE TRIMMED FOR EBAY TAG
   public function createCategoryByName($name,$parent){
  	   $name = $this->clearEbayTag($name);
   	   $parent = $this->clearEbayTag($parent);
	   $t = $this->checkCategoryByName($name);
	   if( $t ){
		   if( $t->getParentCategory()->getName() == $parent || $parent == "")
			   return $t; 
	   }
	   try{
			$category = Mage::getModel('catalog/category');
			$category->setName($name);
			$category->setUrlKey($name);
			$category->setIsActive(1);
			$category->setDisplayMode('PRODUCTS');
			$category->setIsAnchor(1); //for active achor
			$category->setStoreId(Mage::app()->getStore()->getId());
			$parentCategory = Mage::getModel('catalog/category')->loadByAttribute('name',$parent);
			if( $parentCategory ) {
				$category->setPath($parentCategory->getPath());
			}
			else{
				$category->setPath('1');
			}
			$category->save();
		} catch(Exception $e) {
			var_dump($e);
		}
		return $category;
   }
   public function checkCreateCategoryTree($catName){
	   $cats = explode(':' , $catName);
	   $t = $this->createCategoryByName($cats[0],"");
	   for( $i=1;$i<sizeof($cats);$i++){
	   	   $t = $this->createCategoryByName($cats[$i],$cats[$i-1]);
	   } 
	   return $t;
   }
   // REMEMBER TO FIX RATES CURRENCY
   public function priceToEur($price,$currency){
   		$from = $currency;
		$to = Mage::app()->getStore()->getCurrentCurrencyCode();
		$allowedCurrencies = Mage::getModel('directory/currency')->getConfigAllowCurrencies();   
		$currencyRates = Mage::getModel('directory/currency')->getCurrencyRates('EUR', array_values($allowedCurrencies));
		$rate = 1/$currencyRates['USD'];
		$newPrice = $price*$rate;
		return $newPrice;
   }
   public function checkCreateProduct($cat,$sale){
	   
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
		$product->setShortDescription(" ");
		$product->setPrice($this->priceToEur($this->price,$this->currency)*((100+$sale)/100));
		//$product->setTypeId('simple');
		$product->setCategoryIds(array($cat->getId())); 
		$product->setWeight(isset($this->weight)?$this->weight:0.0); // need to fetch weight , I am not currently
		$product->setTaxClassId(2); // taxable goods
		$product->setVisibility(4); // catalog, search
		$product->setStatus(1); // enabled
		// assign product to the default website
		$product->setWebsiteIds(array(Mage::app()->getStore(true)->getWebsite()->getId()));	
		$product->setMetaTitle($this->name);
		

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
   }
}
?>