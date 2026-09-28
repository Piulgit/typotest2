<?php

// Accordion
use B13\Container\Tca\ContainerConfiguration;
use B13\Container\Tca\Registry;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

defined('TYPO3') || die();

GeneralUtility::makeInstance(Registry::class)->configureContainer(
    (
    new ContainerConfiguration(
        'container_accordion', // CType
        'Container Accordion', // label
        'Bootstrap Accordion', // description
        [
            [
                [
                    'name' => 'Accordion Items',
                    'colPos' => 212,
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
    'container_accordion',
    'after:space_after_class'
);
