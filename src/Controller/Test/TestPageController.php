<?php

namespace App\Controller\Test;

use App\Service\ExternalApi\RestfulApiDev;
use App\Service\Import\ProductImport;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    '/test/page',
    name: 'testPage_'
)]
class TestPageController
{

    private RestfulApiDev $restfulApiDev;
    private ProductImport $productImport;
    public function __construct(
        RestfulApiDev $restfulApiDev,
        ProductImport $productImport
    )
    {
        $this->restfulApiDev = $restfulApiDev;
        $this->productImport = $productImport;
    }


    #[Route(
        '/',
        name: 'index',
        methods: ['GET', 'POST']
    )]
    public function testMethod(): Response
    {
        $products = $this->restfulApiDev->getAllProducts();
        echo '<pre>'; print_r($products); echo '</pre>';
        //$this->productImport->importProducts($products);

        return new Response('test');
    }

    #[Route(
        path: [
            'en' => '/about',
            'ru' => '/о-нас'
        ],
        name: 'language_route',
        methods: ['GET'],
    )]
    public function about(): Response
    {
        return new Response(
            "test"
        );
    }
}
