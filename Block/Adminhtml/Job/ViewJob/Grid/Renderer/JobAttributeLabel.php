<?php

namespace Straker\EasyTranslationPlatform\Block\Adminhtml\Job\ViewJob\Grid\Renderer;

use Magento\Backend\Block\Context;
use Magento\Framework\DataObject;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Straker\EasyTranslationPlatform\Helper\BlockHelper;
use Straker\EasyTranslationPlatform\Model\ResourceModel\AttributeTranslation;
use Straker\EasyTranslationPlatform\Model\JobType;
use Straker\EasyTranslationPlatform\Helper\PageHelper;
use Straker\EasyTranslationPlatform\Model\ResourceModel\AttributeTranslation\CollectionFactory as AttributeTranslationCollection;
use Magento\Eav\Model\Entity\AttributeFactory;

class JobAttributeLabel extends AbstractRenderer
{
    protected $_attributeTranslationCollectionFactory;
    /** @var \Straker\EasyTranslationPlatform\Model\ResourceModel\AttributeTranslation\Collection AttributeTranslationCollection  */
    protected $_jobAttributeCollection;
    protected $_attributeFactory;
    protected $_pageHelper;

    public function __construct(
        Context $context,
        AttributeTranslationCollection $attributeTranslationCollection,
        AttributeFactory $attributeFactory,
        PageHelper $pageHelper
    )
    {
        $this->_attributeTranslationCollectionFactory = $attributeTranslationCollection;
        $this->_attributeFactory = $attributeFactory;
        $this->_pageHelper = $pageHelper;
        parent::__construct( $context );
    }

    function render(DataObject $row)
    {
        $isLabel = $row->getData('is_label');
        $jobId = $row->getData('job_id');
        $hasOption = $row->getData('has_option');
        $attrLabel = '';
        $this->_jobAttributeCollection = $this->_attributeTranslationCollectionFactory->create()
            ->addFieldToFilter('job_id', ['eq' => $jobId] )
            ->addfieldtofilter('is_label', ['eq' => true ]);

        if( strcasecmp($isLabel, 'yes') === 0 ){
            if($hasOption){
                $attrLabel = '<a data-attr-id=\''. $row->getData('attribute_translation_id'). '\' class=\'straker-view-option-anchor\'>' . $row->getData('original_value').  '</a>';
            }else{
                $attrLabel = $row->getData('original_value');
            }
        }else{
            $attrLabel = $this->_getFieldLabel( $row->getData('attribute_id'));
        }

        $row->setData('label', $attrLabel );
        return parent::render($row);
    }

    protected function _getFieldLabel( $attributeId ){
        $jobReferrer = $this->getRequest()->getParam('job_type_referrer');

        switch ( $jobReferrer ){
            case JobType::JOB_TYPE_BLOCK:
                $label = BlockHelper::blockAttributes[ $attributeId ]['label'];
                break;
            case JobType::JOB_TYPE_PAGE:
                $label = PageHelper::PageAttributes[ $attributeId ]['label'];
                break;
            default:
                $data = $this->_jobAttributeCollection->addFieldToFilter( 'attribute_id', [ 'eq' => $attributeId ])->getData();
                if (count($data) > 0) {
                    return $data[0]['original_value'];
                }else{
                    return $this->_attributeFactory->create()->load( $attributeId )->getFrontend()->getLabel();
                }
                break;
        }

        return $label;
    }
}
