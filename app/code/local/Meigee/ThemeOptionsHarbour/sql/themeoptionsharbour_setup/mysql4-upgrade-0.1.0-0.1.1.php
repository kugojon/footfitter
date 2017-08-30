<?php
$installer = $this;
$installer->startSetup();
if(Mage::getModel('admin/block')){
	$blockNames = array(
		'newsletter/subscribe'
	);
	foreach ($blockNames as $blockName) {
		$whitelistBlock = Mage::getModel('admin/block')->load($blockName, 'block_name');
		$whitelistBlock->setData('block_name', $blockName);
		$whitelistBlock->setData('is_allowed', 1);
		$whitelistBlock->save();
	}
	
	$variableNames = array(
		'design/footer/copyright'
	);
	foreach ($variableNames as $variableName) {
		$whitelistVar = Mage::getModel('admin/variable')->load($variableName, 'variable_name');
		$whitelistVar->setData('variable_name', $variableName);
		$whitelistVar->setData('is_allowed', 1);
		$whitelistVar->save();
	}
}
$installer->endSetup();