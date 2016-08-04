<?php

namespace Straker\EasyTranslationPlatform\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Straker\EasyTranslationPlatform\Model;

class JobActions extends Column
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (array_key_exists('job_status_id', $item) && array_key_exists('job_id', $item)) {
                    $statusId = $item['job_status_id'];
                    if ($statusId == Model\JobStatus::JOB_STATUS_COMPLETED) {
                        $item[$name]['confirm'] = [
                            'href' => $this->getContext()->getUrl('EasyTranslationPlatform/Jobs/Confirm',
                                ['job_id' => $item['job_id'], 'job_key' => $item['job_key'], 'job_type_id' => $item['job_type_id']]),
                            'label' => __('Confirm')
                        ];
                    }

                    $url = 'EasyTranslationPlatform/Jobs/ViewJob';

                    $item[$name]['view'] = [
                        'href' => $this->getContext()
                            ->getUrl(
                                $url,
                                [
                                    'job_id' => $item['job_id'],
                                    'job_key' => $item['job_key'],
                                    'job_type_id' => $item['job_type_id']
                                ]
                            ),
                        'label' => __('View')
                    ];
                }
            }
        }

        return $dataSource;
    }
}
