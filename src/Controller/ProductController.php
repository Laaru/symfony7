<?php

namespace App\Controller;

use App\Entity\Product;
use App\Message\ProductOperationMessage;
use App\Repository\ColorRepository;
use App\Repository\ProductRepository;
use App\Repository\StoreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

class ProductController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface    $messageBus,
        private readonly ProductRepository      $productRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface     $validator,
        private readonly StoreRepository        $storeRepository,
        private readonly ColorRepository        $colorRepository
    )
    {
    }


    #[Route(
        '/api/product',
        name: 'create_product',
        defaults: ['is_api' => true],
        methods: ['POST']
    )]
    #[OA\Response(
        response: 200,
        description: 'Creates product',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'success',
                    type: 'boolean',
                    default: true
                ),
                new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: Product::class))
                ),
            ],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Parameter error',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'success',
                    type: 'boolean',
                    default: false
                ),
                new OA\Property(
                    property: 'errors',
                    type: 'array',
                    items: new OA\Items(type: 'object'),
                    default: [
                        [
                            'field' => 'slug',
                            'message' => 'This value is already used.'
                        ],
                        [
                            'field' => 'base_price',
                            'message' => 'This value should not be blank.'
                        ]
                    ]
                )
            ],
            type: 'object'
        )
    )]
    #[OA\RequestBody(
        description: 'JSON',
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'name',
                    description: 'required',
                    type: 'string',
                    example: 'keyboard'
                ),
                new OA\Property(
                    property: 'slug',
                    description: 'required',
                    type: 'string',
                    example: 'k_11111'
                ),
                new OA\Property(
                    property: 'description',
                    type: 'string',
                    example: 'description'
                ),
                new OA\Property(
                    property: 'basePrice',
                    description: 'required',
                    type: 'integer',
                    example: 999
                ),
                new OA\Property(
                    property: 'salePrice',
                    type: 'integer',
                    example: 999
                ),
                new OA\Property(
                    property: 'colorSlug',
                    description: 'Символьный код (slug) Цвета (Модель Color)',
                    type: 'string',
                    example: 'black'
                ),
                new OA\Property(
                    property: 'inStockInStores',
                    description: 'Символьные коды (slug) магазинов (Модель Store)',
                    type: 'array',
                    items: new OA\Items(type: 'string'),
                    example: ['store_one', 'store_two']
                ),
            ],
            type: 'object'
        )
    )]
    public function createProduct(Request $request): Response
    {
        $product = new Product();

        $this->updateProductObject($product, $request);
        $errors = $this->validator->validate($product);
        if ($errors->count() > 0) {
            return $this->validationErrorResponse($errors);
        }

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $this->entityManager->persist($product);

        // actually executes the queries (i.e. the INSERT query)
        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'data' => $this->productRepository->normalize($product)
        ]);
    }

    #[Route(
        '/api/product/{id<\d+>?1}', // dynamic id parameter (digits only), equals 1 if not set
        name: 'read_product',
        defaults: ['is_api' => true],
        methods: ['GET'],
        condition: "params['id'] < 1000"
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns product by id',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'success',
                    type: 'boolean',
                    default: true
                ),
                new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: Product::class))
                ),
            ],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Not found error',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'success',
                    type: 'boolean',
                    default: false
                ),
                new OA\Property(
                    property: 'errors',
                    type: 'array',
                    items: new OA\Items(type: 'object'),
                    default: [
                        [
                            'type' => 'Symfony\\Component\\HttpKernel\\Exception\\NotFoundHttpException',
                            'message' => 'No product found for id 178'
                        ]
                    ]
                )
            ],
            type: 'object'
        )
    )]
    public function readProduct(int $id): JsonResponse
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException(
                'No product found for id ' . $id
            );
        }

        $this->messageBus->dispatch(new ProductOperationMessage($id, 'read'));

        return new JsonResponse([
            'success' => true,
            'data' => $this->productRepository->normalize($product)
        ]);
    }

    #[Route(
        '/api/product/{id<\d+>?1}',
        name: 'update_product',
        defaults: ['is_api' => true],
        methods: ['PUT']
    )]
    #[OA\Response(
        response: 200,
        description: 'Updates product',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'success',
                    type: 'boolean',
                    default: true
                ),
                new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: Product::class))
                ),
            ],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Not found error',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'success',
                    type: 'boolean',
                    default: false
                ),
                new OA\Property(
                    property: 'errors',
                    type: 'array',
                    items: new OA\Items(type: 'object'),
                    default: [
                        [
                            'type' => 'Symfony\\Component\\HttpKernel\\Exception\\NotFoundHttpException',
                            'message' => 'No product found for id 178'
                        ]
                    ]
                )
            ],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Parameter error',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'success',
                    type: 'boolean',
                    default: false
                ),
                new OA\Property(
                    property: 'errors',
                    type: 'array',
                    items: new OA\Items(type: 'object'),
                    default: [
                        [
                            'field' => 'slug',
                            'message' => 'This value is already used.'
                        ],
                        [
                            'field' => 'base_price',
                            'message' => 'This value should not be blank.'
                        ]
                    ]
                )
            ],
            type: 'object'
        )
    )]
    #[OA\RequestBody(
        description: 'JSON',
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'name',
                    description: 'required',
                    type: 'string',
                    example: 'keyboard'
                ),
                new OA\Property(
                    property: 'slug',
                    description: 'required',
                    type: 'string',
                    example: 'k_11111'
                ),
                new OA\Property(
                    property: 'description',
                    type: 'string',
                    example: 'description'
                ),
                new OA\Property(
                    property: 'basePrice',
                    description: 'required',
                    type: 'integer',
                    example: 999
                ),
                new OA\Property(
                    property: 'salePrice',
                    type: 'integer',
                    example: 999
                ),
                new OA\Property(
                    property: 'colorSlug',
                    description: 'Символьный код (slug) Цвета (Модель Color)',
                    type: 'string',
                    example: 'black'
                ),
                new OA\Property(
                    property: 'inStockInStores',
                    description: 'Символьные коды (slug) магазинов (Модель Store)',
                    type: 'array',
                    items: new OA\Items(type: 'string'),
                    example: ['store_one', 'store_two']
                ),
            ],
            type: 'object'
        )
    )]
    public function updateProduct(Request $request, int $id): Response
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException(
                'No product found for id ' . $id
            );
        }

        $this->updateProductObject($product, $request);
        $errors = $this->validator->validate($product);
        if ($errors->count() > 0) {
            return $this->validationErrorResponse($errors);
        }

        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'data' => $this->productRepository->normalize($product)
        ]);
    }

    #[Route(
        '/api/product/{id<\d+>?1}',
        name: 'delete_product',
        defaults: ['is_api' => true],
        methods: ['DELETE']
    )]
    #[OA\Response(
        response: 200,
        description: 'Deletes product',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'success',
                    type: 'boolean',
                    default: true
                ),
                new OA\Property(
                    property: 'data',
                    type: 'string',
                    default: 'product removed: test product'
                ),
            ],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Not found error',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'success',
                    type: 'boolean',
                    default: false
                ),
                new OA\Property(
                    property: 'errors',
                    type: 'array',
                    items: new OA\Items(type: 'object'),
                    default: [
                        [
                            'type' => 'Symfony\\Component\\HttpKernel\\Exception\\NotFoundHttpException',
                            'message' => 'No product found for id 178'
                        ]
                    ]
                )
            ],
            type: 'object'
        )
    )]
    public function deleteProduct(int $id): Response
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException(
                'No product found for id ' . $id
            );
        }

        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'data' => [
                'message' => 'product removed: ' . $product->getName()
            ]
        ]);
    }

    #[Route(
        '/product/cheapest/{name}',
        name: 'get_products',
        methods: ['GET']
    )]
    // ProductRepository injected by the dependency injection container
    public function getCheapestProducts(string $name): Response
    {
        $products = $this->productRepository->findCheapestByName($name);

        if (empty($products)) {
            throw $this->createNotFoundException(
                'No product found for name ' . $name
            );
        }

        $result = '';
        foreach ($products as $product) {
            $result .= '<pre>' . print_r($this->productRepository->normalize($product), true) . '</pre>';
        }

        return new Response($result);
    }

    private function updateProductObject(
        Product $product,
        Request $request
    ): void
    {
        $requestParams = json_decode($request->getContent(), true);
        if (
            isset($requestParams['name'])
            && !empty($requestParams['name'])
        ) $product->setName($requestParams['name']);

        if (
            isset($requestParams['slug'])
            && !empty($requestParams['slug'])
        ) $product->setSlug($requestParams['slug']);

        if (
            isset($requestParams['basePrice'])
            && (!empty($requestParams['basePrice']) || $requestParams['basePrice'] === 0)
        ) $product->setBasePrice($requestParams['basePrice']);

        if (
            isset($requestParams['salePrice'])
            && (!empty($requestParams['salePrice']) || $requestParams['salePrice'] === 0)
        ) $product->setSalePrice($requestParams['salePrice']);

        if (
            isset($requestParams['colorSlug'])
            && !empty($requestParams['colorSlug'])
            && $colorFound = $this->colorRepository->findOneBySlug($requestParams['colorSlug'])
        ) $product->setColor($colorFound);

        if (
            isset($requestParams['inStockInStores'])
            && !empty($requestParams['inStockInStores'])
        ) {
            // Remove all current stores
            foreach ($product->getInStockInStores() as $store) {
                $product->removeInStockInStore($store);
            }

            // Add stores from the request
            foreach ($requestParams['inStockInStores'] as $storeSlug) {
                $storeFound = $this->storeRepository->findOneBySlug($storeSlug);
                if ($storeFound)
                    $product->addInStockInStore($storeFound);
            }
        }
    }

    private function validationErrorResponse($errors): JsonResponse
    {
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[] = [
                'field' => $error->getPropertyPath(),
                'message' => $error->getMessage()
            ];
        }

        return new JsonResponse([
            'success' => false,
            'errors' => $errorMessages
        ], Response::HTTP_BAD_REQUEST);
    }
}
