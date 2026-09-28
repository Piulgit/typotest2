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
        'three_columns_layout',
        'Three Columns Layout (33-33-33)',
        'Three Columns Layout by Bootstrap',
        [
            [
                ['name' => 'Left Column', 'colPos' => 205],
                ['name' => 'Middle Column', 'colPos' => 206],
                ['name' => 'Right Column', 'colPos' => 207],
            ],
        ]
    )
    )
        ->setIcon('EXT:sitepackage/Resources/Public/Icons/Extension.svg')
);

ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    '--palette--;;colors',
    'three_columns_layout',
    'after:space_after_class'
);
