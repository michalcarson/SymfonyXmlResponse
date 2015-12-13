<?php
include '../vendor/autoload.php';
use SymfonyXmlResponse\Responses\XmlResponse;
use SymfonyXmlResponse\Responses\XmlRepeater;

$xr = new XmlResponse();

// changing the default root element
$xr->root_element_name = 'lorem';

$data = [
    'ipsum' => 'dolor',
    'sit' => 'amet',
    // create a placeholder for the repeating group
    'files' => '@filesPlaceHolder@',
    'consectetur' => [
        'adipiscing' => [
            'elit' => 'sed',
            'do' => 'eiusmod',
            'tempor' => 'incididunt',
            'ut' => 'labore'
        ]
    ],
    'et' => 'dolore',
    'magna' => 'aliqua'
];

// use an indexed array for the repeating data
$file_array = [
    ['name' => 'file1.txt', 'size' => '10K'],
    ['name' => 'file2.txt', 'size' => '20K'],
    ['name' => 'file3.txt', 'size' => '8K']
];

$repeater = new XmlRepeater('@filesPlaceHolder@', 'file', $file_array);

// be sure to tell XmlResponse to run the decorator
// add as many decorators as needed
$xr->addDecorator($repeater);

// data must be set after the root element
$response = $xr->setData($data);

$response->send();
