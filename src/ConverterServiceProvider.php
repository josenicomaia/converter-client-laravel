<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PRODesign\ConversorClienteLaravel;

use Illuminate\Support\ServiceProvider;
use PRODesign\Converter\Client\PHP\Domain\Converter;
use PRODesign\Converter\Client\PHP\Domain\ConverterConfiguration;
use function str_contains;

/**
 * Description of ConverterServiceProvider
 *
 * @author José Nicodemos Maia Neto<jose at nicomaia.com.br>
 */
class ConverterServiceProvider extends ServiceProvider {
    public function boot() {
        if (!$this->isLumen()) {
            $this->publishes([
                $this->getConfigPath() => config_path('converter.php'),
            ], 'config');
        }
    }
    
    /**
     * 
     * @return string
     */
    private function getConfigPath() {
        return __DIR__ . '/../config/converter.php';
    }
    
    /**
     * 
     * @return bool
     */
    private function isLumen() {
        return str_contains($this->app->version(), 'Lumen');
    }
    
    public function register() {
        $this->mergeConfig();
        $this->registerConverter();
    }
    
    private function mergeConfig() {
        $this->mergeConfigFrom(
            $this->getConfigPath(), 'converter'
        );
    }
    
    private function registerConverter() {
        $this->app->bind(ConverterConfiguration::class, function ($app) {
            $config = $app->make('config')->get('converter', []);
            
            return new ConverterConfiguration(
                    $config['serviceUrl'], 
                    $config['connectTimeout'], 
                    $config['readTimeout']);
        });
        
        $this->app->singleton(Converter::class);
    }
}
