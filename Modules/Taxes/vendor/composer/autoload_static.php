<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf6d5728e5f117bf34ca293c35c1f4509
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'Modules\\Taxes\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Modules\\Taxes\\' => 
        array (
            0 => __DIR__ . '/../..' . '/',
        ),
    );

    public static $classMap = array (
        'Modules\\Taxes\\Database\\Seeders\\TaxesDatabaseSeeder' => __DIR__ . '/../..' . '/Database/Seeders/TaxesDatabaseSeeder.php',
        'Modules\\Taxes\\Datatables\\TaxesDatatable' => __DIR__ . '/../..' . '/Datatables/TaxesDatatable.php',
        'Modules\\Taxes\\Http\\ApiControllers\\TaxesApiController' => __DIR__ . '/../..' . '/Http/ApiControllers/TaxesApiController.php',
        'Modules\\Taxes\\Http\\Controllers\\TaxesController' => __DIR__ . '/../..' . '/Http/Controllers/TaxesController.php',
        'Modules\\Taxes\\Http\\Requests\\CreateTaxesRequest' => __DIR__ . '/../..' . '/Http/Requests/CreateTaxesRequest.php',
        'Modules\\Taxes\\Http\\Requests\\TaxesRequest' => __DIR__ . '/../..' . '/Http/Requests/TaxesRequest.php',
        'Modules\\Taxes\\Http\\Requests\\UpdateTaxesRequest' => __DIR__ . '/../..' . '/Http/Requests/UpdateTaxesRequest.php',
        'Modules\\Taxes\\Models\\Taxes' => __DIR__ . '/../..' . '/Models/Taxes.php',
        'Modules\\Taxes\\Policies\\TaxesPolicy' => __DIR__ . '/../..' . '/Policies/TaxesPolicy.php',
        'Modules\\Taxes\\Presenters\\TaxesPresenter' => __DIR__ . '/../..' . '/Presenters/TaxesPresenter.php',
        'Modules\\Taxes\\Providers\\TaxesServiceProvider' => __DIR__ . '/../..' . '/Providers/TaxesServiceProvider.php',
        'Modules\\Taxes\\Repositories\\TaxesRepository' => __DIR__ . '/../..' . '/Repositories/TaxesRepository.php',
        'Modules\\Taxes\\Transformers\\TaxesTransformer' => __DIR__ . '/../..' . '/Transformers/TaxesTransformer.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf6d5728e5f117bf34ca293c35c1f4509::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf6d5728e5f117bf34ca293c35c1f4509::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitf6d5728e5f117bf34ca293c35c1f4509::$classMap;

        }, null, ClassLoader::class);
    }
}