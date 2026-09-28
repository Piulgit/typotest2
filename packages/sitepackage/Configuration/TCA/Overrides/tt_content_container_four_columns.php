<?php

// Fluid Section - Full width
use B13\Container\Tca\ContainerConfiguration;
use B13\Container\Tca\Registry;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

defined('TYPO3') || die();

GeneralUtility::makeInstance(Registry::class)->configureContainer(
    (
    new \B13\Container\Tca\ContainerConfiguration(
        'four_columns_layout',
        'Four Columns Layout (25%)',
        'Four Columns Layout by Bootstrap',
        [
            [
                ['name' => 'First Column', 'colPos' => 208],
                ['name' => 'Second Column', 'colPos' => 209],
                ['name' => 'Third Column', 'colPos' => 210],
                ['name' => 'Forth Column', 'colPos' => 211],
            ],
        ]
    )
    )
        ->setIcon('EXT:sitepackage/Resources/Public/Icons/Extension.svg')
);

ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    '--palette--;;colors',
    'four_columns_layout',
    'after:space_after_class'
);
