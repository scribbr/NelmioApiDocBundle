<?php

/*
 * This file is part of the ApiDocBundle package.
 *
 * (c) EXSyst
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace EXSyst\Bundle\ApiDocBundle\DependencyInjection;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use phpDocumentor\Reflection\DocBlockFactory;
use Swagger\Annotations\Swagger;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class EXSystApiDocExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'exsyst_api_doc';
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('services.xml');

        // Removes useless services
        if (!class_exists(ApiDoc::class)) {
            $container->removeDefinition('exsyst_api_doc.route_describers.nelmio_annotation');
        }
        if (!class_exists(DocBlockFactory::class)) {
            $container->removeDefinition('exsyst_api_doc.route_describers.php_doc');
        }
        if (!class_exists(Swagger::class)) {
            $container->removeDefinition('exsyst_api_doc.describers.swagger_php');
        }

        $bundles = $container->getParameter('kernel.bundles');
        // ApiPlatform support
        if (isset($bundles['ApiPlatformBundle']) && class_exists('ApiPlatform\Core\Documentation\Documentation')) {
            $loader->load('api_platform.xml');
        }
    }
}