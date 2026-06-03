<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Http\Resources\Json\JsonResource::withoutWrapping();

        Scramble::configure()
            ->afterOpenApiGenerated(function (OpenApi $openApi) {
                $openApi->secure(
                    SecurityScheme::http('bearer')
                );

                foreach ($openApi->paths as $path) {
                    foreach ($path->operations as $operation) {
                        if (strtolower($operation->method) !== 'get') {
                            continue;
                        }

                        $operation->addParameters([
                            \Dedoc\Scramble\Support\Generator\Parameter::make('with_translations', 'query')
                                ->description('If true, returns all translations for translatable fields.')
                                ->example(false)
                                ->setSchema(\Dedoc\Scramble\Support\Generator\Schema::fromType(new \Dedoc\Scramble\Support\Generator\Types\BooleanType)),

                            \Dedoc\Scramble\Support\Generator\Parameter::make('Accept-Language', 'header')
                                ->description('The language of the response requested via header.')
                                ->example('id')
                                ->setSchema(\Dedoc\Scramble\Support\Generator\Schema::fromType(new \Dedoc\Scramble\Support\Generator\Types\StringType)),
                        ]);
                    }
                }
            });
    }
}
