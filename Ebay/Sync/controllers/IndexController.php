<?
require_once ( Mage::getBaseDir('code').'/local/Ebay/Sync/ebay/eBay.php' );
include_once( Mage::getBaseDir('code').'local/Ebay/Sync/ebay/keys.php' );
class Ebay_Sync_IndexController extends Mage_Core_Controller_Front_Action
{
   public function indexAction ()
   {
	   //ini_set('display_errors', 1);
		$this->loadLayout();
		//Release layout stream... lol... sounds fancy
		//$block = $this->getLayout()->createBlock('ebaysync/monblock');
		//var_dump($block);
		if($block)
		{
			$block->setTemplate('ebay/page.phtml');
			//echo "!!!!!!!!!!!!!!!";
			//var_dump($block->toHtml());   
		}    
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
   public function checkSku($sku){
			$id = Mage::getModel('catalog/product')->getIdBySku($sku);
			if ($id){
				return true;
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
   public function checkCreateProduct($item , $cat){
	   
	   try{
	   	Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
		if( $this->checkSku($item->sku) == false ){
			$product = Mage::getModel('catalog/product');

			$product->setAttributeSetId(4); 
			$product->setSku($item->sku);	
			$product->save();
		}
		$pid = Mage::getModel('catalog/product')->getIdBySku($item->sku);
		$product = Mage::getModel('catalog/product')->load($pid);

		$product->setAttributeSetId(4); 
		$product->setSku($item->sku);
		$product->setName($item->name);
		$product->setDescription($item->description);
		$product->setShortDescription(" ");
		$product->setPrice($this->priceToEur($item->price,$item->currency));
		//$product->setTypeId('simple');
		$product->setCategoryIds(array($cat->getId())); 
		$product->setWeight(isset($item->weight)?$item->weight:0.0); // need to fetch weight , I am not currently
		$product->setTaxClassId(2); // taxable goods
		$product->setVisibility(4); // catalog, search
		$product->setStatus(1); // enabled
		// assign product to the default website
		$product->setWebsiteIds(array(Mage::app()->getStore(true)->getWebsite()->getId()));	
		$product->setMetaTitle($item->name);
		$product->save();

		// for stock
		$stockData = $product->getStockData();
		$stockData['qty'] = $item->quantity;
		$stockData['is_in_stock'] = (($item->quantity>0)?1:0);
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
   public function downloadImages($item){
       $pid = Mage::getModel('catalog/product')->getIdBySku($item->sku);
	   $product = Mage::getModel('catalog/product')->load($pid); 

	   for( $i=sizeof($item->pictures)-1;$i>=0;$i--){
			$pic = 		   $item->pictures[$i];
			$image_url  = $pic; //get external image url from csv
			$image_type = substr(strrchr($image_url,"."),1,3); //find the image extension
			$filename   = md5($image_url . $item->sku).'.'.$image_type; //give a new name, you can modify as per your requirement
			$filepath   = Mage::getBaseDir('media') . DS . 'downloadable'. DS . $filename; //path for temp storage folder: ./media/import/
			/// CHECK DONT REDOWNLOAD!!
			if( $this->checkIfImageExists($product , md5($image_url . $item->sku) ) )continue;
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
   public function mamethodeAction ()
   {
	 // GLOBALS INITIATED IN KEYS
	 global $appID,$devID,$certID,$RuName,$serverUrl, $userToken,$compatabilityLevel, $siteID;
	 initKeys();
     // 
	 session_start();
	 $ebay = new Ebay($appID,$devID,$certID,$RuName,$serverUrl, $userToken,$compatabilityLevel, $siteID);
	 $ebay->getStuff();
	 //echo sizeof($ebay->itemIds);
	 $item = new Item();
	 $item = $ebay->getItemData(221455735202);
	
     
	 $cat = $this->checkCreateCategoryTree($item->categoryName);	 
	 
	 $this->checkCreateProduct($item,$cat);
	 $this->downloadImages($item);
	 
	 /* 
	 echo '!';
	 $ebay->ebayManagement();
	 echo "!";
	 */
   }
   public function layoutAction (){
		$this->loadLayout();
		$this->renderLayout();
   }
}

class Item {
	function __construct(){
	return;	
	}
	public function setSku($sku){
		$this->sku = $sku;	
	}	
}
?>