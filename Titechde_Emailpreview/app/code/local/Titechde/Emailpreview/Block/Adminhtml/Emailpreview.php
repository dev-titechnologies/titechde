<?php
/**
 * @category Emailpreview
 * @package Titechde_Emailpreview
 * @authors TiTech <info@titechnologies.in, http://www.titechnologies.de/>
 * @developer TiTech <info@titechnologies.in, http://www.titechnologies.de/>
 * @version 1.0.0
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Titechde_Emailpreview_Block_Adminhtml_Emailpreview extends Mage_Adminhtml_Block_Template
{
    /**
     * Get Order Count Function
     *
     * @return int
     */
    public function getOrderCount()
    {
        $orders = Mage::getSingleton('sales/order')->getCollection();
        if (count($orders) > 0) {
            $singleorder = Mage::getModel('sales/order')->getCollection()
               ->setOrder('entity_id','DESC')
               ->setPageSize(1)
               ->getFirstItem();
            $Returnval =  $singleorder->getEntityId();
        } else {
            $Returnval = 0;
        }
        return $Returnval;
    }

    /**
     * Get Product List Function
     *
     * @return array
     */
    public function getProductList()
    {
        $allproduct = Mage::getModel('catalog/product')
            ->getCollection()
            ->setPageSize(10)
            ->setCurPage(1);
        return $allproduct;
    }
}
