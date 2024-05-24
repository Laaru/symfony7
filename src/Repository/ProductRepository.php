<?php

namespace App\Repository;

use AllowDynamicProperties;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @extends ServiceEntityRepository<Product>
 * @method Product|null findOneBySlug(string $slug)
 */
#[AllowDynamicProperties]
class ProductRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry, NormalizerInterface $normalizer)
    {
        parent::__construct($registry, Product::class);
        $this->normalizer = $normalizer;
    }

    public function findCheapestByName(string $name)
    {
        return $this->createQueryBuilder('product')
            ->where('LOWER(product.name) LIKE LOWER(:name)')
            ->setParameter('name', '%' . $name . '%')
            ->orderBy('product.base_price', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function normalize(Product $product)
    {
        $productData = $this->normalizer->normalize(
            $product,
            null,
            ['groups' => 'entity']
        );
        $productData['color'] = $this->normalizer->normalize(
            $product->getColor()
        );
        $productData['stores'] = $this->normalizer->normalize(
            $product->getInStockInStores(),
            null,
            ['groups' => 'entity']
        );

        return $productData;
    }
}
