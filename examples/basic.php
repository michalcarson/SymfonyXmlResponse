<?php
include '../vendor/autoload.php';
use SymfonyXmlResponse\Responses\XmlResponse;

$xr = new XmlResponse();

// changing the default root element
$xr->root_element_name = 'lorem';

$data = [
    'ipsum' => 'dolor',
    'sit' => 'amet',
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

// data must be set after the root element
$response = $xr->setData($data);

$response->send();
