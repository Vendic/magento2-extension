<?php

namespace Straker\EasyTranslationPlatform\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\DomDocument\DomDocumentFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

class XmlHelper extends AbstractHelper
{
    /** @var string $_version */
    private $_version = "1.0";

    /** @var string $_encoding */
    private $_encoding = 'utf-8';

    /** @var string $_xmlFilePath */
    private $_xmlFilePath;

    /** @var string $_xmlFileName */
    private $_xmlFileName;

    /** @var \DOMDocument $_xmlFileName */
    private $_dom;

    /** @var \DOMElement $_xmlFileName */
    private $_root;

    /** @var \DOMElement $_xmlFileName */
    private $_data;

    private $_elemAttributes = [
        'name', 'content_context', 'content_context_url', 'content_id'
    ];

    public function __construct(
        Context $context,
        DirectoryList $directoryList,
        DomDocumentFactory $domDocumentFactory
    ) {
        $this->_dom = $domDocumentFactory->create();
        $this->_xmlFilePath = $directoryList->getPath('var'). DIRECTORY_SEPARATOR . 'straker';
        parent::__construct($context);
    }

    /**
     * @param $jobId
     * @return bool|\DOMElement
     */
    public function create( $jobId ){
        $this->_dom->version = $this->getVersion();
        $this->_dom->encoding = $this->getEncoding();

        $this->_xmlFileName = $this->_xmlFilePath . DIRECTORY_SEPARATOR . 'job'. $jobId .'.xml';
        $flag = true;

        if( !file_exists( $this->_xmlFilePath ) ){
            $flag = mkdir( $this->_xmlFilePath );
        }

        if( !$flag ) {
            return false;
        }

        if( !file_exists( $this->_xmlFileName ) ){
            $isSuccess = file_put_contents( $this->_xmlFileName, "" );
            if( $isSuccess === false ){
                return false;
            }
        }

        $this->_root = $this->_dom->createElement('root');
        return true;
    }


    /**
     * @param array $attributes
     * @return bool
     */
    public function appendDataToRoot( $attributes = [] ){
        if( !is_array( $attributes )
            || count( $attributes ) <= 0
            || !$this->_validateKeys( $attributes )
        ){
            return false;
        }

        $this->_data = $this->_dom->createElement( 'data' );
        $this->_data->setAttribute( $this->_elemAttributes[0], $attributes['name']);
        $this->_data->setAttribute( $this->_elemAttributes[1], $attributes['content_context']);
        $this->_data->setAttribute( $this->_elemAttributes[2], $attributes['content_context_url']);
        $this->_data->setAttribute( $this->_elemAttributes[3], $attributes['content_id']);

        $valueElem = $this->_dom->createElement( 'value' );
        $valueElem->appendChild( $this->_dom->createCDATASection( $attributes['value'] ) );

        $this->_data->appendChild( $valueElem );
        $this->_root->appendChild( $this->_data );

        return true;
    }

    /**
     * @param array $data
     * @return bool
     */
    private function _validateKeys( $data = [] ){
        return ( 0 === count( array_diff( $this->_elemAttributes, array_keys( $data ) ) ) );
    }
    
    /**
     * @return bool
     */
    public function saveXmlFile()
    {
        $this->_dom->formatOutput = true;
        if( !file_exists( $this->_xmlFileName )){
            return false;
        }
        $this->_dom->appendChild( $this->_root );
        $this->_dom->save( $this->_xmlFileName );
        return true;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->_version;
    }

    /**
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->_version = $version;
    }

    /**
     * @return string
     */
    public function getEncoding()
    {
        return $this->_encoding;
    }

    /**
     * @param string $encoding
     */
    public function setEncoding($encoding)
    {
        $this->_encoding = $encoding;
    }

    /**
     * @return string
     */
    public function getXmlFilePath()
    {
        return $this->_xmlFilePath;
    }

    /**
     * @return string
     */
    public function getXmlFileName()
    {
        return $this->_xmlFileName;
    }

    /**
     * @return \DOMElement
     */
    public function getRoot()
    {
        return $this->_root;
    }

    /**
     * @return array
     */
    public function getElemAttributes()
    {
        return $this->_elemAttributes;
    }

}