<?php
namespace Trinity\NotificationBundle\Command;

use Trinity\NotificationBundle\BunnyManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetupCommand extends Command
{

	/** @var BunnyManager */
	public $manager;

	public function __construct(BunnyManager $manager)
	{
		parent::__construct("bunny:setup");
		$this->manager = $manager;
	}

	protected function configure()
	{
		$this->setDescription("Sets up exchange-queue topology as specified on bunny configuration.");
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->manager->setUp();
	}

}
