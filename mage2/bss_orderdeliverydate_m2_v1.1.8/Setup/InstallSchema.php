<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * BSS Commerce does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BSS Commerce does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   BSS
 * @package    Bss_OrderDeliveryDate
 * @author     Extension Team
 * @copyright  Copyright (c) 2015-2016 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\OrderDeliveryDate\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $installer->getConnection()->addColumn($installer->getTable('quote'), 'shipping_arrival_timeslot', 'text');
        $installer->getConnection()->addColumn($installer->getTable('quote'), 'shipping_arrival_date', 'date');
        $installer->getConnection()->addColumn($installer->getTable('quote'), 'shipping_arrival_comments', 'text');
        $installer->getConnection()->addColumn($installer->getTable('sales_order'), 'shipping_arrival_date', 'date');
        $installer->getConnection()->addColumn($installer->getTable('sales_order'), 'shipping_arrival_comments', 'text');
        $installer->getConnection()->addColumn($installer->getTable('sales_order'), 'shipping_arrival_timeslot', 'text');
        $setup->endSetup();
    }
}
