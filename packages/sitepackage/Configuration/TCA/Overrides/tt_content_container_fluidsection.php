<?php

// Fluid Section - Full width
use B13\Container\Tca\ContainerConfiguration;
use B13\Container\Tca\Registry;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

defined('TYPO3') || die();

GeneralUtility::makeInstance(Registry::class)->configureContainer(
    (
    new ContainerConfiguration(
        'fluid-section', // CType
        'Fluid Section', // label
        'Bootstrap Fluid/Full-Width Section', // description
        [
            [
                [
                    'name' => 'Main Column',
                    'colPos' => 201,
                ],
            ],
        ]
    )
    )
        ->setIcon('EXT:sitepackage/Resources/Public/Icons/Extension.svg')
);

$GLOBALS['TCA']['tt_content']['palettes']['fluidsection_innerspace'] = [
    'label' => 'Inner Spacing (Padding)',
    'showitem' => 'innerspace_before,innerspace_after',
];

ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    '--palette--;;fluidsection_innerspace',
    'fluid-section',
    'after:space_after_class'
);

ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    '--palette--;;colors',
    'fluid-section',
    'after:--palette--;;fluidsection_innerspace'
);
