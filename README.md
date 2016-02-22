CRUDBundle Bundle for CRUD Symfony
====

Installation
------------

minimum require PHP version 5.5, and symfony >= 2.8
````
composer require fardus/crud-bundle
````

In AppKernel.php add
````php
    public function registerBundles() {
      ...
      new Fardus\Bundle\CrudBundle\FardusCrudBundle(),
    }
````

Use
---
With console launch
````shell
    app/console fardus:generate:crud
````

options available
````shell
 --entity=[NameBundle:NameEntity]
 --route-prefix=[/route]
 --format=[xml, yml, annotation, php]

 # If you want to replace the existing code
 --overwrite
````

Component
---

|*name*|*version*|
|Bootstrap| 3.6|
|Font-awesome 4.5
|Jquery| latest version|
