CRUDBundle Bundle for CRUD Symfony
====

Installation
------------
````composer require fardus/crud-bundl````

In AppKernel.php add
````php
    public function registerBundles() {
      ...
      new Fardus\CrudBundle\FardusCrudBundle(),
    }
````

With console launch
````app/console fardus:generate:crud````

options available
````--format=[xml, yml, annotation, php]````
