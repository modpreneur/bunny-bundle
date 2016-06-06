<?php
namespace Trinity\NotificationBundle;

use Bunny\Exception\BunnyException;

class AbstractTransactionalProducer
{

	/** @var string */
	private $exchange;

	/** @var string */
	private $routingKey;

	/** @var boolean */
	private $mandatory;

	/** @var boolean */
	private $immediate;

	/** @var string */
	private $beforeMethod;

	/** @var string */
	private $contentType;

	/** @var boolean */
	private $autoCommit = false;

	/** @var BunnyManager */
	protected $manager;

	public function __construct(
		$exchange,
		$routingKey,
		$mandatory,
		$immediate,
		$beforeMethod,
		$contentType,
		BunnyManager $manager
	) {
		$this->exchange = $exchange;
		$this->routingKey = $routingKey;
		$this->mandatory = $mandatory;
		$this->immediate = $immediate;
		$this->beforeMethod = $beforeMethod;
		$this->contentType = $contentType;
		$this->manager = $manager;
	}

	/**
	 * @param object $message
	 * @param string $routingKey
	 * @param array $headers
	 * @throws BunnyException
	 */
	public function publish($message, $routingKey = null, array $headers = [])
	{
		if ($this->beforeMethod) {
			$this->{$this->beforeMethod}($message, $this->manager->getTransactionalChannel());
		}

		if ($routingKey === null) {
			$routingKey = $this->routingKey;
		}

		$headers["content-type"] = $this->contentType;

		$this->manager->getTransactionalChannel()->publish(
			$message,
			$headers,
			$this->exchange,
			$routingKey,
			$this->mandatory,
			$this->immediate
		);

		if ($this->autoCommit) {
			$this->commit();
		}
	}

	/**
	 * turn on/off automatic commit
	 * @param bool $bool
	 */
	public function setAutoCommit($bool = true)
	{
		$this->autoCommit = $bool;
	}

	/**
	 * commit messages
	 */
	public function commit()
	{
		try {
			$this->manager->getTransactionalChannel()->txCommit();
		} catch (\Exception $e) {
			throw new BunnyException("Cannot commit message.");
		}
	}

	/**
	 * rollback messages
	 */
	public function rollback()
	{
		try {
			$this->manager->getTransactionalChannel()->txRollback();
		} catch (\Exception $e) {
			throw new BunnyException("Cannot rollback message.");
		}
	}

}
