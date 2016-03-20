Nami CMS : CoreBundle
========================

Welcome to the NamiCoreBundle repository - a Symfony 2.7 bundle.

![Nami Logo](https://github.com/phpink/nami-core-bundle/raw/master/Docs/namiLogo.png)

**PhpInk\Nami\CoreBundle** is the main bundle of a Nami CMS application.
It contains the dependencies configuration, *Doctrine ORM/ODM* mapping and *FOSRest* controllers to provide an API.

1) Installation
----------------------------------
    
### Getting started
Run the following command to install the bundle :

    composer require phpink/nami-core-bundle
    
To get NAMI working with MongoDB, add the following line to your composer.json :
    
            "doctrine/mongodb-odm-bundle": "3.0.*@dev",
            
Then, add the following line to your AppKernel.php :
            
            new Doctrine\Bundle\MongoDBBundle\DoctrineMongoDBBundle(),


### Generate new RSA keys \[optional\]
Run the following OpenSSL commands to generate RSA keys for JSON Web Token authentication :

    openssl genrsa -out app/var/jwt/private.pem -aes256 4096
    openssl rsa -pubout -in app/var/jwt/private.pem -out app/var/jwt/public.pem

2) API endpoints
--------------------------------

All available endpoints are available in a [Postman][3] dump
    
    nami.postman_dump
 
Some http client you can run to test the API :

    GET     http://host/api-doc/     REST API documentation [HTML]
    GET     http://host/api/         REST API ping [JSON]
    POST    http://host/api/token    API token getter [JSON]
    GET     http://host/api/pages    API get all method [JSON]
    GET     http://host/api/pages/1  API get one method [JSON]
    POST    http://host/api/pages/1  API update one method [JSON]
    DELETE  http://host/api/pages/1  API delete one method [JSON]

What's inside?
---------------

The bundle is configured with the following defaults:

  * Twig is the only configured template engine;

  * Translations are activated

  * Doctrine ORM/DBAL or Doctrine MongoDB is configured;

  * Swiftmailer is configured;

  * Annotations for everything are enabled.

It comes pre-configured with the following bundles:

  * **FrameworkBundle** - The core Symfony framework bundle

  * [**SensioFrameworkExtraBundle**][4] - Adds several enhancements, including
    template and routing annotation capability

  * [**DoctrineBundle**][5] - Adds support for the Doctrine ORM

  * [**DoctrineMongoDBBundle**][6] - Adds support for the Doctrine ODM

  * [**DoctrineExtensions**][7] - Doctrine2 behavioral extensions

  * [**DoctrineFixtures**][8] (in dev/test env) - Load data fixtures into the Doctrine ORM

  * [**TwigBundle**][9] - Adds support for the Twig templating engine

  * [**SecurityBundle**][10] - Adds security by integrating Symfony's security component

  * [**SwiftmailerBundle**][11] - Adds support for Swiftmailer, a library for sending emails

  * [**MonologBundle**][12] - Adds support for Monolog, a logging library

  * [**AsseticBundle**][13] - Adds support for Assetic, an asset processing
    library

  * **WebProfilerBundle** (in dev/test env) - Adds profiling functionality and
    the web debug toolbar

  * **SensioDistributionBundle** (in dev/test env) - Adds functionality for
    configuring and working with Symfony distributions

  * [**SensioGeneratorBundle**][15] (in dev/test env) - Adds code generation capabilities

  * [**FOSRestBundle**][16] - Adds rest functionality

  * [**FOSHttpCacheBundle**][21] - This bundle offers tools to improve HTTP caching with Symfony2

  * [**NelmioApiDocBundle**][17] - Add API documentation features

  * [**BazingaHateoasBundle**][18] - Adds HATEOAS support

  * [**HautelookTemplatedUriBundle**][19] - Adds Templated URIs (RFC 6570) support

  * [**BazingaRestExtraBundle**][20]

  * [**LexikJWTAuthenticationBundle**][21] - JSON Web Token generation

  * [**GfreeauGetJWTBundle**][22] - JSON Web Token authentication

  * [**LiipImagineBundle**][23] - Image thumbnail generation

Enjoy!


API inner working
---------------

### Controllers

CoreBundle controllers extend a base `AbstractController`, which contains a set of methods for the Doctrine CRUD worflow.
Associated Doctrine model/repository & FormType class names are guessed automatically.

For example: `NamiCoreBundle:Model\Orm\User` entity & `NamiCoreBundle:Form\UserType` for `NamiCoreBundle:Controller\UserController`.

The doctrine repository used by the controller to run the generic CRUD commands is the one associated by default to the model.
A different one can be called  :

```php
public function getEntitiesAction() {
    $productRepo = $this->getRepository('Product'); // From model name
    // or
    $productRepo = $this->getRepository('\PhpInk\Nami\CoreBundle\Model\Orm\Products'); // More specific
    ...
    return $this->restView(...); // Returns a JSON response
}
```

### Repositories

Core repositories extend a base `AbstractRepository`, which contains all the CRUD methods called from controllers.
Repositories can overload the base properties, such as `orderByFields`, `filterByFields`.

They must implement a `buildItemsQuery` method that will be called to create the Doctrine QueryBuilder retrieving one or more item from the database.

As for creation & update, the controllers instantiate the form type related to the associated model.


### Json TO Forms

A JsonDecoder is implemented and modifies the input request data.
It is called from the FosRest body listener (configuration is in
`src/PhpInk/CoreBundle/DependencyInjection/NamiCoreBundleExtension.php`).

It transforms json input booleans `true,false` into optional `0,1` checkboxes for FormTypes.

To execute specific code after an item creation or update, take a look at the `UserController::onPostSave` method that sends the user confirmation mail.

[1]:  http://symfony.com/doc/2.1/book/installation.html
[2]:  http://getcomposer.org/
[3]:  https://www.getpostman.com/
[4]:  http://symfony.com/doc/2.6/bundles/SensioFrameworkExtraBundle/index.html
[5]:  http://symfony.com/doc/2.6/book/doctrine.html
[6]:  https://github.com/doctrine/DoctrineMongoDBBundle
[7]:  https://github.com/Atlantic18/DoctrineExtensions
[8]:  https://github.com/doctrine/DoctrineFixturesBundle
[9]:  http://symfony.com/doc/2.6/book/templating.html
[10]:  http://symfony.com/doc/2.6/book/security.html
[11]: http://symfony.com/doc/2.6/cookbook/email.html
[12]: http://symfony.com/doc/2.6/cookbook/logging/monolog.html
[13]: http://symfony.com/doc/2.6/cookbook/assetic/asset_management.html
[15]: http://symfony.com/doc/2.6/bundles/SensioGeneratorBundle/index.html
[16]: https://github.com/FriendsOfSymfony/FOSRestBundle
[17]: https://github.com/nelmio/NelmioApiDocBundle
[18]: https://github.com/willdurand/BazingaHateoasBundle
[19]: https://github.com/hautelook/TemplatedUriBundle
[20]: https://github.com/willdurand/BazingaRestExtraBundle
[21]: https://github.com/lexik/LexikJWTAuthenticationBundle
[22]: https://github.com/gfreeau/GfreeauGetJWTBundle
[23]: https://github.com/liip/LiipImagineBundle
[24]: https://angularjs.org/
[25]: http://getbootstrap.com/
