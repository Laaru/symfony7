<?php

namespace App\Command;

use App\Service\ExternalApi\RestfulApiDev;
use App\Service\Import\ProductImport;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'import:products')]
class ImportProductsCommand extends Command
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

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Imports products')
            ->setHelp('Imports products from external API');

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            '---starting---',
            '-initializing product request'
        ]);

        $products = $this->restfulApiDev->getAllProducts();
        if (empty($products)) {
            $output->writeln([
                '-No products received from external api',
                '-see log for additional info',
                '---exiting---'
            ]);
            return Command::FAILURE;
        }

        $output->writeln([
            '-products received',
            '-starting import'
        ]);

        $this->productImport->importProducts($products);

        $output->writeln([
            '-import finished',
            '-see log for additional info',
            '---exiting---'
        ]);

        return Command::SUCCESS;
    }
}
