<?php

namespace App\Service\Import;

use AllowDynamicProperties;
use App\Entity\Product;
use App\Repository\ColorRepository;
use App\Repository\ProductRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class ProductImport
{

    private Slugify $slugify;
    private ColorRepository $colorRepository;
    private ProductRepository $productRepository;
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;
    private LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface     $validator,
        ProductRepository      $productRepository,
        ColorRepository        $colorRepository,
        LoggerInterface        $logger
    )
    {
        $this->slugify = new Slugify();
        $this->colorRepository = $colorRepository;
        $this->productRepository = $productRepository;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->logger = $logger;
    }

    public function importProducts(array $products): void
    {
        $this->logger->info('Import started. Products to import: ' . count($products));

        $importedProducts = 0;
        foreach ($products as $productData) {
            $productData = array_change_key_case($productData, CASE_LOWER);

            if (!isset($productData['name']) || empty($productData['name'])) {
                $this->logger->warning('Product without name. Id: ' . $productData['id']);
                continue;
            }

            if (isset($productData['data']) && is_array($productData['data'])) {
                $productData = array_merge($productData, array_change_key_case($productData['data'], CASE_LOWER));

                $description = '';
                foreach ($productData['data'] as $name => $additionalInfo) {
                    if (!empty($description)) $description .= '. ';
                    $description .= $name . ': ' . $additionalInfo;
                }
            }


            if (!isset($productData['price']) || empty($productData['price']))
                $productData['price'] = 0;

            $product = new Product();
            $product->setName($productData['name']);
            $product->setSlug($this->slugify->slugify($productData['name']));
            if (isset($productData['price']))
                $product->setBasePrice((int)$productData['price']);
            if (!empty($description))
                $product->setDescription($description);

            if (
                isset($productData['color'])
                && !empty($productData['color'])
                && $colorFound = $this->colorRepository->findOneBySlug($productData['color'])
            ) $product->setColor($colorFound);

            $errors = $this->validator->validate($product);
            if ($errors->count() > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = [
                        'field' => $error->getPropertyPath(),
                        'message' => $error->getMessage()
                    ];
                }
                $this->logger->warning(
                    'Product id: ' . $productData['id'] . ' validation errors',
                    [
                        'errors' => $errorMessages,
                        'product' => $this->productRepository->normalize($product)
                    ]
                );
                continue;
            }
            $this->entityManager->persist($product);
            $this->entityManager->flush();
            $importedProducts++;
        }

        $this->logger->info('Import finished. Imported products: ' . $importedProducts);
    }
}
