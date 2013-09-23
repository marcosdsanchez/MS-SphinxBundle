<?php

namespace MS\SphinxBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SphinxExtension extends Extension
{
	public function load(array $configs, ContainerBuilder $container)
	{
		$processor = new Processor();
		$configuration = new Configuration();

		$config = $processor->processConfiguration($configuration, $configs);

		$loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

		$loader->load('sphinx.xml');

		/**
		 * Indexer.
		 */
		if( isset($config['indexer']) ) {
			$container->setParameter('sphinx.indexer.bin', $config['indexer']['bin']);
		}

		/**
		 * Indexes.
		 */
		$container->setParameter('sphinx.indexes', $config['indexes']);

		/**
		 * Searchd.
		 */
		if( isset($config['searchd']) ) {
			$container->setParameter('sphinx.searchd.host', $config['searchd']['host']);
			$container->setParameter('sphinx.searchd.port', $config['searchd']['port']);
			$container->setParameter('sphinx.searchd.socket', $config['searchd']['socket']);
		}
	}

	public function getAlias()
	{
		return 'sphinx';
	}
}
