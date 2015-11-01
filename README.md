# CRUDBundle
Bundle for CRUD Symfony

Installation
------------
`composer require fardus/crud-bundle`

In AppKernel.php add
``
public function registerBundles() {
  ...
  new Fardus\CrudBundle\FardusCrudBundle(),
}
``

With console launch
`app/console fardus:generate:crud`

options available
`--format=[xml, yml, annotation, php]`
