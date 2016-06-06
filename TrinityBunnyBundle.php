<?php
namespace Trinity\NotificationBundle;

use Doctrine\Common\Annotations\AnnotationReader;
use Trinity\NotificationBundle\DependencyInjection\Compiler\BunnyCompilerPass;
use Trinity\NotificationBundle\DependencyInjection\SkrzBunnyExtension;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class TrinityBunnyBundle extends Bundle
{

	public function getContainerExtension()
	{
		if ($this->extension === null) {
			$this->extension = new SkrzBunnyExtension();
		}

		return $this->extension;
	}

	public function build(ContainerBuilder $container)
	{
		$container->addCompilerPass(
			new BunnyCompilerPass(
				"bunny",
				"bunny.client",
				"bunny.manager",
				"bunny.channel",
				"command.bunny.setup",
				"command.bunny.consumer",
				"command.bunny.producer",
				new AnnotationReader()
			),
			PassConfig::TYPE_OPTIMIZE
		);
	}

	public function registerCommands(Application $application)
	{
		/** @var Command[] $commands */
		$commands = [
			$this->container->get("command.bunny.setup"),
			$this->container->get("command.bunny.consumer"),
			$this->container->get("command.bunny.producer"),
		];

		foreach ($commands as $command) {
			$application->add($command);
		}
	}

}