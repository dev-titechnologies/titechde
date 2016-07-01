<?php
/**
 * @category Emailpreview
 * @package Titechde_Emailpreview
 * @authors TiTech <info@titechnologies.in, http://www.titechnologies.de/>
 * @developer TiTech <info@titechnologies.in, http://www.titechnologies.de/>
 * @version 1.0.0
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Titechde_Emailpreview_Adminhtml_EmailpreviewController extends Mage_Adminhtml_Controller_Action
{
    /**
    * Init action
    *
    * @access public
    * @return void
    */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('emailpreview/emailpreview');
        return $this;
    }

    /**
    * Index action
    *
    * @return void
    */
    public function indexAction()
    {
        //$this->_initAction();
        $this->loadLayout('popup');
        $this->getLayout()->unsetBlock('footer');
        $this->_title($this->__("Titech Emailpreview"));
        $myblock = $this->getLayout()->createBlock('emailpreview/adminhtml_emailpreview')->setTemplate('emailpreview/mailform.phtml');
        $this->_addContent($myblock);
        $this->renderLayout();
    }

    /**
    * Set Session Function action
    *
    * @return boolean
    */
    public function setajaxAction()
    {
        Mage::getSingleton('core/session')->setMytype($this->getRequest()->getParam('type'));
        Mage::getSingleton('core/session')->setMytext($this->getRequest()->getParam('text'));
        Mage::getSingleton('core/session')->setMystyle($this->getRequest()->getParam('styles'));
        Mage::getSingleton('core/session')->setMysubject($this->getRequest()->getParam('subject'));
        return true;
    }

    /**
    * Create Product action
    *
    * @return void
    */
    public function createProductAction()
    {
        $proid = Mage::getModel('emailpreview/emailpreview')->createnewproduct();
        $this->_redirect('*/*/index');
    }

    /**
    * Preview action
    *
    * @return void
    */
    public function previewAction()
    {
        $this->loadLayout('popup');
        $this->renderLayout();
    }

    /**
    * Create order action
    *
    * @return void
    */
    public function createOrderAction()
    {
        $email      = $this->getRequest()->getParam('testemail');
        $orderid    = $this->getRequest()->getParam('orderid');
        if ($orderid == 0) {
            $product    = array();
            $product1   = $this->getRequest()->getParam('product1');
            $product2   = $this->getRequest()->getParam('product2');
            if ($product1) {
                $product[0] = $product1;
            }
            if ($product2) {
                $product[1] = $product2;
            }
            $paymethod  = $this->getRequest()->getParam('payment');
            $shipmethod = $this->getRequest()->getParam('shipping');

            $ordid = Mage::getModel('emailpreview/HandleOrderCreate')->create($product, $email, $paymethod, $shipmethod);
        } else {
            $ordid = $orderid;
        }

        $parameters = array('order_id'=>$ordid,'toemail'=>$email);
        $this->_forward('preview',NULL,NULL,$parameters);
    }
}
