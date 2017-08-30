<?php
$installer = $this;
$installer->startSetup();
// Mega Menu
$installer->addAttribute('catalog_category', 'meigee_cat_block_bottom', array(
    'group'             => 'Meigee/Enhanced Categories',
    'label'             => 'Bottom Content',
    'note'              => "<strong style='color:red'>May be used for top-level categories only.</strong><br />This content will be shown under submenu.",
    'type'              => 'text',
    'input'             => 'textarea',
    'visible'           => true,
    'required'          => false,
    'backend'           => '',
    'frontend'          => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'user_defined'      => true,
    'visible_on_front'  => true,
    'wysiwyg_enabled'   => true,
    'is_html_allowed_on_front'  => true,
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->addAttribute('catalog_category', 'meigee_cat_block_top', array(
    'group'             => 'Meigee/Enhanced Categories',
    'label'             => 'Top Content',
    'note'              => "<strong style='color:red'>May be used for top-level categories only.</strong><br />This content will be shown above submenu.",
    'type'              => 'text',
    'input'             => 'textarea',
    'visible'           => true,
    'required'          => false,
    'backend'           => '',
    'frontend'          => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'user_defined'      => true,
    'visible_on_front'  => true,
    'wysiwyg_enabled'   => true,
    'is_html_allowed_on_front'  => true,
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->endSetup();