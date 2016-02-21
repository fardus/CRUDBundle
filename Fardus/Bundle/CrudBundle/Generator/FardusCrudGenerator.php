<?php

namespace Fardus\Bundle\CrudBundle\Generator;

use Sensio\Bundle\GeneratorBundle\Generator\DoctrineCrudGenerator;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * Description of FardusCrudGenerator
 *
 * @author jpetit
 */
class FardusCrudGenerator extends DoctrineCrudGenerator
{
    /**
     * @var BundleInterface
     */
    protected $bundle;

    /**
     * @var ClassMetadataInfo
     */
    protected $metadata;

    /**
     * @param \Symfony\Component\HttpKernel\Bundle\BundleInterface $bundle
     * @param string                                               $entity
     * @param \Doctrine\ORM\Mapping\ClassMetadataInfo              $metadata
     * @param string                                               $format
     * @param string                                               $routePrefix
     * @param array                                                $needWriteActions
     * @param                                                      $forceOverwrite
     */
    public function generate(
        BundleInterface $bundle,
        $entity,
        ClassMetadataInfo $metadata,
        $format,
        $routePrefix,
        $needWriteActions,
        $forceOverwrite
    ) {
        $this->routePrefix     = $routePrefix;
        $this->routeNamePrefix = str_replace('/', '_', $routePrefix);
        $this->actions         = $needWriteActions ? ['index', 'show', 'new', 'edit', 'delete', 'search'] : [
            'index',
            'show',
            'search',
        ];

        if (count($metadata->identifier) > 1) {
            throw new \RuntimeException('The CRUD generator does not support entity classes with multiple primary keys.');
        }

        if (!in_array('id', $metadata->identifier)) {
            throw new \RuntimeException('The CRUD generator expects the entity object has a primary key field named "id" with a getId() method.');
        }

        $this->entity   = $entity;
        $this->bundle   = $bundle;
        $this->metadata = $metadata;
        $this->setFormat($format);

        $this->generateControllerClass($forceOverwrite);

        $dir = sprintf('%s/Resources/views/%s', $this->bundle->getPath(), str_replace('\\', '/', $this->entity));

        if (!file_exists($dir)) {
            $this->filesystem->mkdir($dir, 0777);
        }

        $this->generateIndexView($dir);

        if (in_array('show', $this->actions)) {
            $this->generateShowView($dir);
        }

        if (in_array('new', $this->actions)) {
            $this->generateNewView($dir);
        }

        if (in_array('edit', $this->actions)) {
            $this->generateEditView($dir);
        }

        $this->generateTestClass();
        $this->generateConfiguration();

        $this->copyBasicTemplates()
             ->copyBasicTranslations()
             ->generateCommonView($dir)
        ;

        if (in_array('delete', $this->actions)) {
            $this->generateDeleteView($dir);
        }
    }

    /**
     * copyBasicTemplates
     *
     * @return static
     */
    protected function copyBasicTranslations()
    {
        $pathFiles = [
            "crud_common.en.yml" => "crud_common.en.yml",
            "crud_common.fr.yml" => "crud_common.fr.yml",
        ];

        return $this->copyBasic('Resources/translations/', $pathFiles);
    }

    /**
     * copyBasicTemplates
     *
     * @return static
     */
    protected function copyBasicTemplates()
    {
        $pathFiles = [
            "base.html.twig"       => "fardus.crud.base.html.twig",
            "pagination.html.twig" => "fardus.crud.pagination.html.twig",
            "search.html.twig"     => "fardus.crud.search.html.twig",
        ];

        return $this->copyBasic('Resources/views/', $pathFiles);
    }

    /**
     * copyBasicTemplates
     *
     * @return static
     */
    protected function copyBasic($repository, array $pathFiles)
    {
        $dir = sprintf('%s/%s', $this->bundle->getPath(), $repository);

        foreach ($pathFiles as $baseFile => $pathFile) {
            $pathFileAbsolute = $dir.$pathFile;

            if (!$this->filesystem->exists($pathFileAbsolute)) {
                $this->filesystem->copy(__DIR__.'/../'.$repository.$baseFile, $pathFileAbsolute);
            }
        }

        return $this;
    }

    /**
     * Generates the delete.html.twig template in the final bundle.
     *
     * @param string $dir The path to the folder that hosts templates in the bundle
     */
    protected function generateDeleteView($dir)
    {
        $this->renderFile('crud/views/delete.html.twig.twig', $dir.'/delete.html.twig', [
            'route_prefix'      => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'entity'            => $this->entity,
            'bundle'            => $this->bundle->getName(),
            'actions'           => $this->actions,
        ]);
    }

    /**
     * Generates the Common.html.twig template in the final bundle.
     *
     * @param string $dir The path to the folder that hosts templates in the bundle
     */
    protected function generateCommonView($dir)
    {
        $this->renderFile('crud/views/common.html.twig.twig', $dir.'/common.html.twig', [
            'route_prefix'      => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'entity'            => $this->entity,
            'bundle'            => $this->bundle->getName(),
            'actions'           => $this->actions,
        ]);
    }

    /**
     * Generates the controller class only.
     *
     * @param bool $forceOverwrite
     */
    protected function generateControllerClass($forceOverwrite)
    {
        $dir = $this->bundle->getPath();

        $parts           = explode('\\', $this->entity);
        $entityClass     = array_pop($parts);
        $entityNamespace = implode('\\', $parts);

        $target = sprintf(
            '%s/Controller/%s/%sController.php',
            $dir,
            str_replace('\\', '/', $entityNamespace),
            $entityClass
        );

        if (!$forceOverwrite && file_exists($target)) {
            throw new \RuntimeException('Unable to generate the controller as it already exists.');
        }

        $this->renderFile('crud/controller.php.twig', $target, [
            'actions'           => $this->actions,
            'route_prefix'      => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'bundle'            => $this->bundle->getName(),
            'entity'            => $this->entity,
            'entity_class'      => $entityClass,
            'namespace'         => $this->bundle->getNamespace(),
            'entity_namespace'  => $entityNamespace,
            'format'            => $this->format,
            'fields'            => $this->metadata->fieldMappings,
        ]);
    }

    /**
     * Sets the configuration format.
     *
     * @param string $format The configuration format
     */
    protected function setFormat($format)
    {
        switch ($format) {
            case 'yml':
            case 'xml':
            case 'php':
            case 'annotation':
                $this->format = $format;
                break;
            default:
                $this->format = 'yml';
                break;
        }
    }

}
