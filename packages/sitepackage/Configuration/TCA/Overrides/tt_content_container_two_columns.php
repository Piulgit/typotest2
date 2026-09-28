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
        'two_columns_layout',
        'Two Columns Layout (50-50)',
        'Two Columns Layout by Bootstrap',
        [
            [
                ['name' => 'Left Column', 'colPos' => 203],
                ['name' => 'Right Column', 'colPos' => 204],
            ],
        ]
    )
    )
        ->setIcon('EXT:sitepackage/Resources/Public/Icons/Extension.svg')
);

ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    '--palette--;;colors',
    'two_columns_layout',
    'after:space_after_class'
);
