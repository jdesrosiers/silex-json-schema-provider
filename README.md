silex-json-schema-provider
==========================
[![Build Status](https://travis-ci.org/jdesrosiers/silex-json-schema-provider.svg)](https://travis-ci.org/jdesrosiers/silex-json-schema-provider)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jdesrosiers/silex-json-schema-provider/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jdesrosiers/silex-json-schema-provider/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/jdesrosiers/silex-json-schema-provider/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/jdesrosiers/silex-json-schema-provider/?branch=master)

silex-json-schema-provider is a Silex service provider for working with JSON Schema.

Installation
------------
Install the silex-json-schema-provider using [composer](http://getcomposer.org/).  This project uses
[sematic versioning](http://semver.org/).

```bash
composer require jdesrosiers/silex-json-schema-provider "~1.0"
```

Parameters
----------
* **json-schema.correlationMechanism**: ("profile" or "link")  Defaults to "link".
* **json-schema.describedBy**: (string) A URI identifying the location of a schema that describes the response.

Services
--------
* **json-schema.schema-store**: An instance of SchemaStore as described here https://github.com/geraintluff/jsv4-php.
* **json-schema.validator**: An object that exposes the Jsv4 methods described here
https://github.com/geraintluff/jsv4-php.

Registering
-----------
```php
$app->register(new JDesrosiers\Silex\Provider\JsonSchemaServiceProvider());
```

JSON Validation
---------------
JSON Schema validation is supported by https://github.com/geraintluff/jsv4-php.

```php
$schemaJson = <<<SCHEMA
{
    "$schema": "http://json-schema.org/draft-04/hyper-schema#",
    "type": "object",
    "properties": {
        "id": {
            "type": "string"
        }
    }
}
SCHEMA;
$app["json-schema.schema-store"]->add("/schema/foo", json_decode($schemaJson));
$schema = $app["json-schema.schema-store"]->get("/schema/foo");
$validation = $app["json-schema.validator"]->validate($data, $schema);
```

Correlation
-----------
The JSON Schema specification has two recommendations for correlating a schema to a resource.  This service provider
registers after middleware that supports both.  See http://json-schema.org/latest/json-schema-core.html#anchor33 for
more information on schema correlation.  Set the `$app["json-schema.describedBy"]` parameter to the schema that the
response should be correlated to.

```php
$app->get("/foo/{id}", function ($id) use ($app) {
    $app["json-schema.describedBy"] = "/schema/foo";
    return JsonResponse::create(array("id" => $id));
});
```

Full Example
-------------
```php
$app["json-schema.schema-store"]->add("/schema/foo", $app["schemaRepository"]->fetch("foo"));

$app->put("/foo/{id}", function (Request $request, $id) use ($app) {
    $data = json_decode($request->getContent());

    $schema = $app["json-schema.schema-store"]->get("/schema/foo");
    $validation = $app["json-schema.validator"]->validate($data, $schema);
    if (!$validation->valid) {
        $error = array("validationErrors" => $validation->errors);
        return JsonResponse::create($error, Response::HTTP_BAD_REQUEST);
    }

    $isCreated = !$app["fooRepository"]->contains($id);
    $app["fooRepository"]->save($id, $data);

    $app["json-schema.describedBy"] = "/schema/foo";
    return JsonResponse::create($data, $isCreated ? Response::HTTP_CREATED : Response::HTTP_OK);
});
```
