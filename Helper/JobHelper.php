<?php

namespace Straker\EasyTranslationPlatform\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Data\Collection;

use Straker\EasyTranslationPlatform\Helper\ConfigHelper;
use Straker\EasyTranslationPlatform\Helper\ProductHelper;
use Straker\EasyTranslationPlatform\Model\JobFactory;
use Straker\EasyTranslationPlatform\Model\ResourceModel\JobType\CollectionFactory as JobTypeCollection;
use Straker\EasyTranslationPlatform\Model\ResourceModel\JobStatus\CollectionFactory as JobStatusCollection;

class JobHelper extends AbstractHelper
{

    protected $jobModel;

    protected $jobData;

    protected $jobFileName;

    protected $productData;

    public function __construct(
        Context $context,
        JobFactory $jobFactory,
        ProductHelper $productHelper,
        ConfigHelper $configHelper,
        JobTypeCollection $jobTypeCollectionFactory,
        JobStatusCollection $jobStatusFactory

    ) {

        $this->_jobFactory = $jobFactory;
        $this->_configHelper = $configHelper;
        $this->_productHelper = $productHelper;
        $this->_jobTypeCollection = $jobTypeCollectionFactory;
        $this->_jobStatusCollection = $jobStatusFactory;
        parent::__construct($context);
    }

    /**
     * @param $data
     * @return $this
     */
    public function createJob($data)
    {

        $this->jobData = $data;
//        var_dump( $data  );exit();
        $this->jobModel = $this->_jobFactory->create();

        $this->jobModel->setData(
            [
                'job_status_id'=>$this->getJobStatusId('init'),
                'source_store_id'=>$this->_configHelper->getStoreInfo($this->jobData['destination_store'])['straker/general/source_store'],
                'target_store_id'=>$this->jobData['destination_store'],
                'sl'=>$this->_configHelper->getStoreInfo($this->jobData['destination_store'])['straker/general/source_language'],
                'tl'=>$this->_configHelper->getStoreInfo($this->jobData['destination_store'])['straker/general/destination_language']
            ]
        );
//        var_dump( $this->jobModel->getData() );exit();
        return $this;
    }

    /**
     * @return $this
     */
    public function generateProductJob()
    {

        $this->jobModel->addData(['job_type_id'=>$this->getJobTypeId('product')]);

        $this->jobModel->save();

        $jobFile = $this->_productHelper->getProducts($this->jobData['products'],$this->jobModel->getData('source_store_id'))
            ->getSelectedProductAttributes()
            ->saveProductData($this->jobModel->getId())
            ->generateProductXML($this->jobModel);

        $this->jobModel->addData(['source_file'=>$jobFile]);

        $this->jobModel->save();

        return $this;
    }

    /**
     * @return mixed
     */
    public function getJobInfo(){

        return $this->jobModel->getData();
    }

    /**
     * @return mixed
     */
    public function getJob(){

        return $this->jobModel;
    }

    /**
     * @return $this
     */
    public function save()
    {
        $this->jobModel->save();

        return $this;
    }

    /**
     * @param $jobType
     * @return mixed
     */
    protected function getJobTypeId($jobType){

        $collection = $this->_jobTypeCollection->create()
            ->addFieldToFilter('type_name',array('eq'=>$jobType))
            ->getFirstItem();

        return $collection->getData('type_id');
    }

    /**
     * @param $jobStatus
     * @return mixed
     */
    protected function getJobStatusId($jobStatus){

        $collection = $this->_jobStatusCollection->create()
            ->addFieldToFilter('status_name',array('eq'=>$jobStatus))
            ->getFirstItem();

        return $collection->getData('status_id');
    }

}