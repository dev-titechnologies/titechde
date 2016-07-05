<?php
/**
 * @category Emailpreview
 * @package Titechde_Emailpreview
 * @authors TiTech <info@titechnologies.in, http://www.titechnologies.de/>
 * @developer TiTech <info@titechnologies.in, http://www.titechnologies.de/>
 * @version 1.0.0
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Titechde_Emailpreview_Block_Adminhtml_Templatepreview extends Mage_Adminhtml_Block_Widget
{
    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        $receiveEmail   = $this->getRequest()->getParam('toemail');
        $receiveName    = $this->getRequest()->getParam('toemail');
        $OrderId        = $this->getRequest()->getParam('order_id'); //If exist will get the id else 0


        // Start store emulation process
        // Since the Transactional Email preview process has no mechanism for selecting a store view to use for
        // previewing, use the default store view
        $defaultStoreId         = Mage::app()->getDefaultStoreView()->getId();

        $senderEmail    = Mage::getStoreConfig('trans_email/ident_general/email', $defaultStoreId); //Default store email id
        $senderName     = Mage::getStoreConfig('trans_email/ident_general/name', $defaultStoreId); //Default store user name


        $appEmulation           = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($defaultStoreId);

        /** @var $template Mage_Core_Model_Email_Template */
        $template = Mage::getModel('core/email_template');
        $template->setTemplateType(Mage::getSingleton('core/session')->getMytype());
        $template->setTemplateText(Mage::getSingleton('core/session')->getMytext());
        $template->setTemplateStyles(Mage::getSingleton('core/session')->getMystyle());
        $template->setTemplateSubject(Mage::getSingleton('core/session')->getMysubject());

        /* @var $filter Mage_Core_Model_Input_Filter_MaliciousCode */
        $filter = Mage::getSingleton('core/input_filter_maliciousCode');

        $template->setTemplateText(
            $filter->filter($template->getTemplateText())
        );

        $template->setTemplateSubject(
            $filter->filter($template->getTemplateSubject())
        );

        Varien_Profiler::start("email_template_proccessing");

        $emailTemplateVars                  = array();
        $emailTemplateVars['usermessage']   = "Sample order";
        $emailTemplateVars['store']         = Mage::app()->getStore();
        $emailTemplateVars['sendername']    = $senderName;
        $emailTemplateVars['receivername']  = $receiveName;

        // order you want to load by ID
        $emailTemplateVars['order'] = Mage::getModel('sales/order')->load($OrderId);

        // load payment details:
        // usually rendered by this template:
        // web/app/design/frontend/base/default/template/payment/info/default.phtml
        $order          = $emailTemplateVars['order'];
        $paymentBlock   = Mage::helper('payment')->getInfoBlock($order->getPayment())
                            ->setIsSecureMode(true);
        $paymentBlock->getMethod()->setStore(Mage::app()->getStore());
        $emailTemplateVars['payment_html'] = $paymentBlock->toHtml();

        //displays the rendered email template
        $templateProcessed = $template->getProcessedTemplate($emailTemplateVars, true);
        $template->setSenderEmail(Mage::getStoreConfig('trans_email/ident_general/email', $defaultStoreId));
        $template->setSenderName(Mage::getStoreConfig('trans_email/ident_general/name', $defaultStoreId));

        //And finally, we send out the email:
        try{
            //Confimation E-Mail Send
            $template->send($receiveEmail,$receiveName, $emailTemplateVars);
         }
         catch(Exception $error)
         {
            //Mage::getSingleton('core/session')->addError($error->getMessage());
            return $error->getMessage();
         }

        //Unset the session
        Mage::getSingleton('core/session')->unsMytype();
        Mage::getSingleton('core/session')->unsMytext();
        Mage::getSingleton('core/session')->unsMystyle();
        Mage::getSingleton('core/session')->unsMysubject();


        //show the preview
        if ($template->isPlain()) {
            $templateProcessed = "<pre>" . htmlspecialchars($templateProcessed) . "</pre>";
        }

        Varien_Profiler::stop("email_template_proccessing");

        // Stop store emulation process
        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        return $templateProcessed;
    }
}
