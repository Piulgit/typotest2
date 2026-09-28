<?php

// Fluid Section - Full width
use B13\Container\Tca\ContainerConfiguration;
use B13\Container\Tca\Registry;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

defined('TYPO3') || die();

// Fluid Section - Full width
GeneralUtility::makeInstance(Registry::class)->configureContainer(
    (
    new ContainerConfiguration(
        'container_section', // CType
        'Container Section', // label
        'Bootstrap Container', // description
        [
            [
                [
                    'name' => 'Main Column',
                    'colPos' => 202,
                ],
            ],
        ]
    )
    )
        ->setIcon('EXT:sitepackage/Resources/Public/Icons/Extension.svg')
);

ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    '--palette--;;colors',
    'container_section',
    'after:space_after_class'
);
