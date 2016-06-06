<?php

namespace JDesrosiers\Silex\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use SchemaStore;
use Silex\Application;
use Silex\Api\BootableProviderInterface;

class JsonSchemaServiceProvider implements ServiceProviderInterface, BootableProviderInterface
{
    public function boot(Application $app)
    {
        $app->after(new DescribedBy());
    }

    public function register(Container $app)
    {
        $app["json-schema.correlationMechanism"] = "profile";

        $app["json-schema.schema-store"] = function () {
            return new SchemaStore();
        };

        $app["json-schema.validator"] = function () {
            return new Jsv4Validator();
        };
    }
}
