<?php

namespace Straker\EasyTranslationPlatform\Block\Adminhtml\Setup\ProductAttributes;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

class FormContainer extends \Magento\Backend\Block\Widget\Form\Container
{

    protected $_mode = 'form';


    public function __construct(
        Context $context,
        Registry $registry
    )
    {
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_blockGroup = 'Straker_EasyTranslationPlatform';
        $this->_controller = 'adminhtml_setup_ProductAttributes';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save'));

        $this->buttonList->remove('reset');
    }

    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

}