<?php
$installer = $this;
$installer->startSetup();

$installer->run("
ALTER TABLE `sales_flat_order`
	ADD COLUMN `client_po` VARCHAR(50) NULL DEFAULT NULL ,
	ADD COLUMN `custom_order_id` VARCHAR(50) NULL DEFAULT NULL AFTER `client_po`;
	
ALTER TABLE `sales_flat_order_grid`
	ADD COLUMN `client_po` VARCHAR(50) NULL DEFAULT NULL ,
	ADD COLUMN `custom_order_id` VARCHAR(50) NULL DEFAULT NULL AFTER `client_po`;

");
$installer->endSetup();
	 