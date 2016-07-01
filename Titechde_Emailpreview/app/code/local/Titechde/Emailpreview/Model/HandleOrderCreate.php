<?php
/**
 * @category Emailpreview
 * @package Titechde_Emailpreview
 * @authors TiTech <info@titechnologies.in, http://www.titechnologies.de/>
 * @developer TiTech <info@titechnologies.in, http://www.titechnologies.de/>
 * @version 1.0.0
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Titechde_Emailpreview_Model_HandleOrderCreate extends Mage_Core_Model_Abstract
{
    /**
    * Create New Order Function
    *
    * @return void
    */
    public function create($product, $email, $paymethod, $shipmethod)
    {
        try {
            $productids = $product;
            $websiteId = Mage::app()->getWebsite('base')->getId();
            $store = Mage::app()->getStore('default');
            // Start New Sales Order Quote
            // $quote = Mage::getModel('sales/quote')->setStoreId($store->getId());
            $quote = Mage::getModel('sales/quote')->setStoreId(Mage::app()->getStore('default')->getId());

            // Set Sales Order Quote Currency
            //$quote->setCurrency($order->AdjustmentAmount->currencyID);
            $customer = Mage::getModel('customer/customer')
                    ->setWebsiteId($websiteId)
                    ->loadByEmail($email);
            if ($customer->getId() == "") {
                $customer = Mage::getModel('customer/customer');
                $customer->setWebsiteId($websiteId)
                        ->setStore($store)
                        ->setFirstname('Sandra')
                        ->setLastname('Flora')
                        ->setEmail($email)
                        ->setPassword("password");
                $customer->save();
            }

            // Assign Customer To Sales Order Quote
            $quote->assignCustomer($customer);

            // Configure Notification
            $quote->setSendCconfirmation(1);
            foreach ($productids as $id)
            {
                $product = Mage::getModel('catalog/product')->load($id);
                $quote->addProduct($product, new Varien_Object(array('qty' => 1)));
            }

            // Set Sales Order Billing Address
            $billingAddress = $quote->getBillingAddress()->addData(array(
                'customer_address_id' => '',
                'prefix' => '',
                'firstname' => 'Sandra',
                'middlename' => '',
                'lastname' => 'Flora',
                'suffix' => '',
                'company' => '',
                'street' => array(
                    '0' => 'Lange Reihe 29',
                    '1' => 'Sector 64'
                ),
                'city' => 'Hamburg',
                'country_id' => 'DE',
                'region' => '81',
                'postcode' => '20099',
                'telephone' => '78676789',
                'fax' => 'gghlhu',
                'vat_id' => '',
                'save_in_address_book' => 1
                    ));

            // Set Sales Order Shipping Address
            $shippingAddress = $quote->getShippingAddress()->addData(array(
                'customer_address_id' => '',
                'prefix' => '',
                'firstname' => 'Sandra',
                'middlename' => '',
                'lastname' => 'Flora',
                'suffix' => '',
                'company' => '',
                'street' => array(
                    '0' => 'Lange Reihe 29',
                    '1' => 'Sector 64'
                ),
                'city' => 'Hamburg',
                'country_id' => 'DE',
                'region' => '81',
                'postcode' => '20099',
                'telephone' => '78676789',
                'fax' => 'gghlhu',
                'vat_id' => '',
                'save_in_address_book' => 1
                    ));

            if ($shipmethod == '') {
                $shipmethod = 'flatrate_flatrate';
            }

            if ($paymethod == '') {
                $paymethod = 'checkmo';
            }

            // Collect Rates and Set Shipping & Payment Method
            $shippingAddress->setCollectShippingRates(true)
                    ->collectShippingRates()
                    ->setShippingMethod($shipmethod)
                    ->setPaymentMethod($paymethod);

            // Set Sales Order Payment
            $quote->getPayment()->importData(array('method' => $paymethod));

            // Collect Totals & Save Quote
            $quote->collectTotals()->save();


            // Create Order From Quote
            $service = Mage::getModel('sales/service_quote', $quote);
            $service->submitAll();
            $order = $service->getOrder();

            $increment_id = $order->getIncrementId();
            $orderId = $order->getEntityId();
        }
        catch (Exception $ex) {
            return $ex->getMessage();
        }
        catch (Mage_Core_Exception $e) {
            return $e->getMessage();
        }
        // Resource Clean-Up
        $quote = $customer = $service = null;
        // Finished
        return $orderId;
    }
}
