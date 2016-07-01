<?php
/**
 * @category Emailpreview
 * @package Titechde_Emailpreview
 * @authors TiTech <info@titechnologies.in, http://www.titechnologies.de/>
 * @developer TiTech <info@titechnologies.in, http://www.titechnologies.de/>
 * @version 1.0.0
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Titechde_Emailpreview_Model_Emailpreview extends Mage_Core_Model_Abstract
{
    /**
    * Create New Product Function
    *
    * @return void
    */
    public function createnewproduct()
    {
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        $product    = Mage::getModel('catalog/product');
        $websiteId  = Mage::app()->getWebsite('base')->getId();
        $storeId    = Mage::app()->getStore('default')->getId();

        //Set SKU dynamically over here
        $collection = Mage::getModel('catalog/product')
                    ->getCollection()
                    ->addAttributeToSort('created_at', 'desc');
        $collection->getSelect()->limit(1);
        $latestItemId = $collection->getLastItem()->getId();
        if($latestItemId) {
            $nextProid = $latestItemId + 1;
        } else {
            $nextProid = 1;
        }
        $SampleSKU = 'titechPro'.$nextProid;

        try {
            $product
                ->setStoreId($storeId) //you can set data in store scope
                ->setWebsiteIds(array($websiteId)) //website ID the product is assigned to, as an array
                ->setAttributeSetId(4) //ID of a attribute set named 'default'
                ->setTypeId('simple') //product type
                ->setCreatedAt(strtotime('now')) //product creation time
                ->setUpdatedAt(strtotime('now')) //product update time
                ->setSku($SampleSKU) //SKU
                ->setName('Titech sample product') //product name
                ->setWeight(4.0000)
                ->setStatus(1) //product status (1 - enabled, 2 - disabled)
                ->setTaxClassId(4) //tax class (0 - none, 1 - default, 2 - taxable, 4 - shipping)
                ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH) //catalog and search visibility
                //->setManufacturer(28) //manufacturer id
                //->setColor(24)
                //->setNewsFromDate('06/26/2014') //product set as new from
                //->setNewsToDate('06/30/2014') //product set as new to
                ->setCountryOfManufacture('IN') //country of manufacture (2-letter country code)
                ->setPrice(11.22) //price in form 11.22
                ->setCost(22.33) //price in form 11.22
                //->setSpecialPrice(00.44) //special price in form 11.22
                //->setSpecialFromDate('06/1/2014') //special price from (MM-DD-YYYY)
                //->setSpecialToDate('06/30/2014') //special price to (MM-DD-YYYY)
                ->setMsrpEnabled(1) //enable MAP
                ->setMsrpDisplayActualPriceType(1) //display actual price (1 - on gesture, 2 - in cart, 3 - before order confirmation, 4 - use config)
                ->setMsrp(99.99) //Manufacturer's Suggested Retail Price
                ->setMetaTitle('Sample meta title 2')
                ->setMetaKeyword('Sample meta keyword 2')
                ->setMetaDescription('Sample meta description 2')
                ->setDescription('This is a long description for sample product')
                ->setShortDescription('This is a short description for sample product')
                ->setMediaGallery (array('images'=>array (), 'values'=>array ())) //media gallery initialization
                ->addImageToMediaGallery('media/catalog/product/ti/ti_logo.png', array('image','thumbnail','small_image'), false, false) //assigning image, thumb and small image to media gallery
                ->setStockData(array(
                                   'use_config_manage_stock' => 0,  //'Use config settings' checkbox
                                   'manage_stock'=>1,               //manage stock
                                   'min_sale_qty'=>1,               //Minimum Qty Allowed in Shopping Cart
                                   'max_sale_qty'=>2,               //Maximum Qty Allowed in Shopping Cart
                                   'is_in_stock' => 1,              //Stock Availability
                                   'qty' => 999                     //qty
                               )
                )
                ->setCategoryIds(array(2)); //assign product to categories - Set to Default

                $product->save();
                return $product->getIdBySku($SampleSKU);
        } catch(Exception $e) {
            Mage::log($e->getMessage());
        }
    }
}
