<?php

namespace Mtownsend\ResponseXml\Providers;

use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Spatie\ArrayToXml\ArrayToXml;

class ResponseXmlServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->isLumen() ? $this->loadLumenResponseMacros() : $this->loadLaravelResponseMacros();
    }

    /**
     * If the application is Laravel, load Laravel's response factory and macro the xml methods
     *
     */
    protected function loadLaravelResponseMacros()
    {
        \Illuminate\Routing\ResponseFactory::macro('xml', function ($xml, $status = 200, array $headers = [], $xmlRoot = 'response') {
            if (is_array($xml)) {
                $xml = ArrayToXml::convert($xml, $xmlRoot);
            } elseif (is_object($xml) && method_exists($xml, 'toArray')) {
                $xml = ArrayToXml::convert($xml->toArray(), $xmlRoot);
            } elseif (is_string($xml)) {
                $xml = $xml;
            } else {
                $xml = '';
            }
            if (!isset($headers['Content-Type'])) {
                $headers = array_merge($headers, ['Content-Type' => 'application/xml']);
            }
            return \Illuminate\Routing\ResponseFactory::make($xml, $status, $headers);
        });

        \Illuminate\Routing\ResponseFactory::macro('preferredFormat', function ($data, $status = 200, array $headers = [], $xmlRoot = 'response') {
            $request = Container::getInstance()->make('request');
            if (Str::contains($request->headers->get('Accept'), 'xml')) {
                return $this->xml($data, $status, array_merge($headers, ['Content-Type' => $request->headers->get('Accept')]), $xmlRoot);
            } else {
                return $this->json($data, $status, array_merge($headers, ['Content-Type' => $request->headers->get('Accept')]));
            }
        });
    }

    /**
     * If the application is Lumen, load Lumens's response factory and macro the xml methods
     *
     */
    protected function loadLumenResponseMacros()
    {
        \Laravel\Lumen\Http\ResponseFactory::macro('xml', function ($xml, $status = 200, array $headers = [], $xmlRoot = 'response') {
            if (is_array($xml)) {
                $xml = ArrayToXml::convert($xml, $xmlRoot);
            } elseif (is_object($xml) && method_exists($xml, 'toArray')) {
                $xml = ArrayToXml::convert($xml->toArray(), $xmlRoot);
            } elseif (is_string($xml)) {
                $xml = $xml;
            } else {
                $xml = '';
            }
            if (!isset($headers['Content-Type'])) {
                $headers = array_merge($headers, ['Content-Type' => 'application/xml']);
            }
            return \Laravel\Lumen\Http\ResponseFactory::make($xml, $status, $headers);
        });

        \Laravel\Lumen\Http\ResponseFactory::macro('preferredFormat', function ($data, $status = 200, array $headers = [], $xmlRoot = 'response') {
            $request = Container::getInstance()->make('request');
            if (Str::contains($request->headers->get('Accept'), 'xml')) {
                return $this->xml($data, $status, array_merge($headers, ['Content-Type' => $request->headers->get('Accept')]), $xmlRoot);
            } else {
                return $this->json($data, $status, array_merge($headers, ['Content-Type' => $request->headers->get('Accept')]));
            }
        });
    }

    protected function isLumen()
    {
        if (!isset($this->app)) {
            return false;
        }
        return Str::contains($this->app->version(), 'Lumen');
    }
}
