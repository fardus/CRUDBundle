CRUDBundle Bundle for CRUD Symfony
====

Installation
------------
````
composer require fardus/crud-bundl
````

In AppKernel.php add
````php
    public function registerBundles() {
      ...
      new Fardus\Bundle\CrudBundle\FardusCrudBundle(),
    }
````

With console launch
````shell
    app/console fardus:generate:crud
````

options available
````shell
 --format=[xml, yml, annotation, php]
````
