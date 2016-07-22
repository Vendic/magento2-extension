<?php
namespace Straker\EasyTranslationPlatform\Setup;

use Magento\Eav\Model\EavCustomAttributeTypeLocator;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Store\Model\Store;
use Straker\EasyTranslationPlatform\Model;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        //START straker_job_type setup
        if (!$installer->tableExists(Model\JobType::ENTITY)) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable(Model\JobType::ENTITY)
            )->addColumn(
                'type_id',
                Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true,],
                'Entity ID'
            )->addColumn(
                'type_name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false,],
                'Name'
            )->addColumn(
                'creation_time',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT,],
                'Creation Time'
            )->addColumn(
                'update_time',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE,],
                'Modification Time'
            )->addColumn(
                'is_active',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '1',],
                'Is Active'
            )->addIndex(
                $installer->getIdxName(Model\JobType::ENTITY, ['type_id'], AdapterInterface::INDEX_TYPE_UNIQUE),
                'type_id',
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            );
            $installer->getConnection()->createTable($table);
        }
        //END   table setup

        //START straker_job_status setup
        if (!$installer->tableExists(Model\JobStatus::ENTITY)) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable(Model\JobStatus::ENTITY)
            )->addColumn(
                'status_id',
                Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true,],
                'Entity ID'
            )->addColumn(
                'status_name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false,],
                'Name'
            )->addColumn(
                'creation_time',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT,],
                'Creation Time'
            )->addColumn(
                'update_time',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE,],
                'Modification Time'
            )->addColumn(
                'is_active',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '1',],
                'Is Active'
            )->addIndex(
                $installer->getIdxName(Model\JobStatus::ENTITY, ['status_id'], AdapterInterface::INDEX_TYPE_UNIQUE),
                'status_id',
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            );
            $installer->getConnection()->createTable($table);
        }
        //END   table setup

        //setup straker_job
        if (!$installer->tableExists(Model\Job::ENTITY)) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable(Model\Job::ENTITY)
            )->addColumn(
                'job_id',
                Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                'Job Id'
            )->addColumn(
                'job_type_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Job Type Id'
            )->addColumn(
                'source_store_id',
                Table::TYPE_SMALLINT,
                5,
                ['nullable' => false],
                'Source Store Id'
            )->addColumn(
                'target_store_id',
                Table::TYPE_SMALLINT,
                5,
                ['unsigned' => true, 'nullable' => false],
                'Target Store Id'
            )->addColumn(
                'job_number',
                Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Job Number'
            )->addColumn(
                'source_language',
                Table::TYPE_TEXT,
                100,
                ['nullable' => false],
                'Source Language'
            )->addColumn(
                'target_language',
                Table::TYPE_TEXT,
                100,
                ['nullable' => false],
                'Target Language'
            )->addColumn(
                'job_key',
                Table::TYPE_TEXT,
                100,
                ['nullable' => false],
                'Job Key'
            )->addColumn(
                'job_status_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Job Status Id'
            )->addColumn(
                'source_file',
                Table::TYPE_TEXT,
                255,
                [],
                'Source Fileh'
            )->addColumn(
                'download_url',
                Table::TYPE_TEXT,
                255,
                [],
                'Download Url'
            )->addColumn(
                'remote_version',
                Table::TYPE_TEXT,
                10,
                ['default' => ''],
                'File extension'
            )->addColumn(
                'download_version',
                Table::TYPE_TEXT,
                10,
                ['default' => ''],
                'File extension'
            )->addColumn(
                'creation_time',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT,],
                'Creation Time'
            )->addColumn(
                'update_time',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE,],
                'Modification Time'
            )->addColumn(
                'is_active',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '1',],
                'Is Active'
            )->addIndex(
                $installer->getIdxName(Model\Job::ENTITY, ['job_id'], AdapterInterface::INDEX_TYPE_UNIQUE),
                'job_id',
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )->setComment(
                'Job table'
            )->addForeignKey(
                $installer->getFkName(Model\Job::ENTITY, 'target_store_id', Store::ENTITY, 'store_id'),
                'target_store_id',
                Store::ENTITY,
                'store_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(Model\Job::ENTITY, 'job_type_id', Model\JobType::ENTITY, 'type_id'),
                'job_type_id',
                Model\JobType::ENTITY,
                'type_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(Model\Job::ENTITY, 'job_status_id', Model\JobStatus::ENTITY, 'status_id'),
                'job_status_id',
                Model\JobStatus::ENTITY,
                'status_id',
                Table::ACTION_CASCADE
            );

            $installer->getConnection()->createTable($table);
        }

//        if (!$installer->tableExists('straker_product_attachment_rel')) {
//            $table = $installer->getConnection()
//                ->newTable($installer->getTable('straker_product_attachment_rel'))
//                ->addColumn('job_id', Table::TYPE_INTEGER, 10, ['nullable' => false, 'unsigned' => true])
//                ->addColumn('product_id', Table::TYPE_INTEGER, 10, ['nullable' => false, 'unsigned' => true],
//                    'Magento Product Id')
//                ->addForeignKey(
//                    $installer->getFkName(
//                        'straker_job',
//                        'job_id',
//                        'straker_product_attachment_rel',
//                        'job_id'
//                    ),
//                    'job_id',
//                    $installer->getTable('straker_job'),
//                    'job_id',
//                    Table::ACTION_CASCADE
//                )
//                ->addForeignKey(
//                    $installer->getFkName(
//                        'straker_product_attachment_rel',
//                        'job_id',
//                        'catalog_product_entity',
//                        'entity_id'
//                    ),
//                    'product_id',
//                    $installer->getTable('catalog_product_entity'),
//                    'entity_id',
//                    Table::ACTION_CASCADE
//                )
//                ->setComment('Straker Product Attachment relation table');
//
//            $installer->getConnection()->createTable($table);
//        }

        //START straker_attribute_transltion setup
        if (!$installer->tableExists(Model\AttributeTranslation::ENTITY)) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable(Model\AttributeTranslation::ENTITY)
            )->addColumn(
                'attribute_transltion_id',
                Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true,],
                'Transltion ID'
            )->addColumn(
                'job_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Job Id'
            )->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Entity Id'
            )->addColumn(
                'attribute_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Attribute Id'
            )->addColumn(
                'has_option',
                Table::TYPE_BOOLEAN,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Attribute Option Id'
            )->addColumn(
                'original_value',
                Table::TYPE_TEXT,
                Table::MAX_TEXT_SIZE,
                ['nullable' => true],
                'Original Text'
            )->addColumn(
                'transleted_value',
                Table::TYPE_TEXT,
                Table::MAX_TEXT_SIZE,
                ['nullable' => true],
                'Transltion done by Straker'
            )->addColumn(
                'is_imported',
                Table::TYPE_BOOLEAN,
                null,
                ['nullable' => true],
                'Is Imported'
            )->addColumn(
                'import_time',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => true],
                'Import Time'
            )->addColumn(
                'creation_time',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT,],
                'Creation Time'
            )->addColumn(
                'update_time',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE,],
                'Modification Time'
            )->addColumn(
                'is_active',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '1',],
                'Is Active'
            )->addIndex(
                $installer->getIdxName(Model\AttributeTranslation::ENTITY, ['attribute_transltion_id'],
                    AdapterInterface::INDEX_TYPE_UNIQUE),
                'attribute_transltion_id',
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )->addForeignKey(
                $installer->getFkName(Model\AttributeTranslation::ENTITY, 'job_id', Model\Job::ENTITY, 'job_id'),
                'job_id',
                Model\Job::ENTITY,
                'job_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(Model\AttributeTranslation::ENTITY, 'attribute_id', 'eav_attribute',
                    'attribute_id'),
                'attribute_id',
                'eav_attribute',
                'attribute_id',
                Table::ACTION_CASCADE
            );
            $installer->getConnection()->createTable($table);
        }
        //END   table setup

        //START straker_attribute_transltion setup
        if (!$installer->tableExists(Model\AttributeOptionTranslation::ENTITY)) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable(Model\AttributeOptionTranslation::ENTITY)
            )->addColumn(
                'attribute_option_transltion_id',
                Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true,],
                'Transltion ID'
            )->addColumn(
                'attribute_transltion_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Attribute Id'
            )->addColumn(
                'option_id',
                Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Option Id'
            )->addColumn(
                'original_value',
                Table::TYPE_TEXT,
                Table::MAX_TEXT_SIZE,
                ['nullable' => true],
                'Original Text'
            )->addColumn(
                'transleted_value',
                Table::TYPE_TEXT,
                Table::MAX_TEXT_SIZE,
                ['nullable' => true],
                'Transltion done by Straker'
            )->addColumn(
                'is_imported',
                Table::TYPE_BOOLEAN,
                null,
                ['nullable' => true],
                'Is Imported'
            )->addColumn(
                'import_time',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => true],
                'Import Time'
            )->addColumn(
                'creation_time',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT,],
                'Creation Time'
            )->addColumn(
                'update_time',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE,],
                'Modification Time'
            )->addColumn(
                'is_active',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '1',],
                'Is Active'
            )->addIndex(
                $installer->getIdxName(Model\AttributeOptionTranslation::ENTITY, ['attribute_option_transltion_id'],
                    AdapterInterface::INDEX_TYPE_UNIQUE),
                'attribute_option_transltion_id',
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )->addForeignKey(
                $installer->getFkName(Model\AttributeOptionTranslation::ENTITY, 'attribute_transltion_id', Model\AttributeTranslation::ENTITY, 'attribute_transltion_id'),
                'attribute_transltion_id',
                Model\AttributeTranslation::ENTITY,
                'attribute_transltion_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(Model\AttributeOptionTranslation::ENTITY, 'option_id', 'eav_attribute_option', 'option_id'),
                'option_id',
                'eav_attribute_option',
                'option_id',
                Table::ACTION_CASCADE
            );
            $installer->getConnection()->createTable($table);
        }
        //END   table setup

        $installer->endSetup();
    }
}
