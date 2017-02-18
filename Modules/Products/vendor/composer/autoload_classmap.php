<?php

// autoload_classmap.php @generated by Composer

$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

return array(
    'Modules\\Products\\Database\\Seeders\\ProductsDatabaseSeeder' => $baseDir . '/Database/Seeders/ProductsDatabaseSeeder.php',
    'Modules\\Products\\Datatables\\ProductsDatatable' => $baseDir . '/Datatables/ProductsDatatable.php',
    'Modules\\Products\\Http\\ApiControllers\\ProductsApiController' => $baseDir . '/Http/ApiControllers/ProductsApiController.php',
    'Modules\\Products\\Http\\Controllers\\ProductsController' => $baseDir . '/Http/Controllers/ProductsController.php',
    'Modules\\Products\\Http\\Requests\\CreateProductsRequest' => $baseDir . '/Http/Requests/CreateProductsRequest.php',
    'Modules\\Products\\Http\\Requests\\ProductsRequest' => $baseDir . '/Http/Requests/ProductsRequest.php',
    'Modules\\Products\\Http\\Requests\\UpdateProductsRequest' => $baseDir . '/Http/Requests/UpdateProductsRequest.php',
    'Modules\\Products\\Models\\Products' => $baseDir . '/Models/Products.php',
    'Modules\\Products\\Policies\\ProductsPolicy' => $baseDir . '/Policies/ProductsPolicy.php',
    'Modules\\Products\\Presenters\\ProductsPresenter' => $baseDir . '/Presenters/ProductsPresenter.php',
    'Modules\\Products\\Providers\\ProductsServiceProvider' => $baseDir . '/Providers/ProductsServiceProvider.php',
    'Modules\\Products\\Repositories\\ProductsRepository' => $baseDir . '/Repositories/ProductsRepository.php',
    'Modules\\Products\\Transformers\\ProductsTransformer' => $baseDir . '/Transformers/ProductsTransformer.php',
);