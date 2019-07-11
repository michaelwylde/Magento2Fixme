<?php
namespace Swimwear\Fixme\Console\Command;

class Fixme extends \Symfony\Component\Console\Command\Command
{
    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterfaceFactory $productRepository
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('swimwear:fixme')
            ->setDescription('Please fixme');
    }
}
