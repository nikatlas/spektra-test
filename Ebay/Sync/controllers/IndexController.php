<?
require_once ( '/home/schoolik/public_html/magento/app/code/local/Ebay/Sync/ebay/eBay.php' );
include_once( '/home/schoolik/public_html/magento/app/code/local/Ebay/Sync/ebay/keys.php' );
class Ebay_Sync_IndexController extends Mage_Core_Controller_Front_Action
{
   public function indexAction ()
   {
	 
		$new_product = Mage::getModel('catalog/product')->loadByAttribute('sku', 1025423456);
	 
		$new_product->setPrice(10.42);
		/*$new_product->setCategoryIds(array(1152));
		$new_product->setAttributeSetId(4); 
		$new_product->setVisibility(4);             
		$new_product->setType('SiProduct');
		$new_product->setName("Script Created");
		$new_product->setDescription('test');
		$new_product->setShortDescription('test');
		$new_product->setStatus(1); 
		$new_product->setTaxClassId(2);
		$new_product->setWeight(110);             
		$new_product->setCreatedAt(strtotime('now'));                   
	*/
		$visibility = array (
		  'thumbnail',
		  'small_image',
		  'image'
		  );
		$filePath =  Mage::getBaseDir('media') . '/downloadable/test.jpg';
		$new_product->addImageToMediaGallery($filePath,$visibility,true,false);
		// call save() method to save your product with updated data
		$new_product->save();
	 
     echo 'test index';
   }
   public function mamethodeAction ()
   {
	 // GLOBALS INITIATED IN KEYS
	 global $appID,$devID,$certID,$RuName,$serverUrl, $userToken,$compatabilityLevel, $siteID;
	 initKeys();
     // 
	 session_start();
	 $ebay = new Ebay($appID,$devID,$certID,$RuName,$serverUrl, $userToken,$compatabilityLevel, $siteID);

	 if( !isset($_SESSION['sesId']) || $_SESSION['sesId'] == "" ){
	 	include_once("/home/schoolik/public_html/magento/app/code/local/Ebay/Sync/ebay/front.php");
	 }
	 else{
		include_once("/home/schoolik/public_html/magento/app/code/local/Ebay/Sync/ebay/frontR.php"); 
	 }
	 /*
	 echo '!';
	 $ebay->ebayManagement();
	 echo "!";
	 */
   }
}
?>