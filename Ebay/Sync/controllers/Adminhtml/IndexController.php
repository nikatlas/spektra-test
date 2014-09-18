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
	 $store = $_REQUEST['store'];
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
	 Mage::app()->setCurrentStore($store);
	 $cat = $item->checkCreateCategoryTree($item->categoryName,$store);		
	 $item->checkCreateProduct($cat,$sale,$store);
	 $item->downloadImages();	
	 echo "0";
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
   public function createCategoryByName($name,$parent,$store){
  	   $name = $this->clearEbayTag($name);
   	   $parent = (is_integer($parent))? $parent : $this->clearEbayTag($parent);
	   $t = $this->checkCategoryByName($name);
	   
	   if( $t ){
		   if( is_integer($parent) ){
				if( $t->getParentCategory()->getId() == $parent )return $t;   
		   }
		   else{
			   if( $t->getParentCategory()->getName() == $parent || $parent == Mage::app()->getStore($store)->getRootCategoryId())
				   return $t; 
		   }
	   }
	   try{
			$category = Mage::getModel('catalog/category');
			$category->setStoreId($store);
			$category->setName($name);
			$category->setUrlKey($name);
			$category->setIsActive(1);
			$category->setDisplayMode('PRODUCTS');
			$category->setIsAnchor(1); //for active achor
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
		$rate = 1/$currencyRates['USD'];
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
		$product->setTaxClassId(2); // taxable goods
		$product->setVisibility(4); // catalog, search
		$product->setStatus(1); // enabled
		$product->setPromotion(0); // enabled
		$product->setDesignstyle(11); // enabled
		// assign product to the default website
		$product->setWebsiteIds(array($store));	
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