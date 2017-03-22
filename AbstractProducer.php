<?php
namespace Trinity\Bundle\BunnyBundle;

class AbstractProducer
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

    /** @var BunnyManager */
    protected $manager;

    /**
     * AbstractProducer constructor.
     *
     * @param              $exchange
     * @param              $routingKey
     * @param              $mandatory
     * @param              $immediate
     * @param              $beforeMethod
     * @param              $contentType
     * @param BunnyManager $manager
     */
    public function __construct($exchange, $routingKey, $mandatory, $immediate, $beforeMethod, $contentType, BunnyManager $manager)
    {
        $this->exchange = $exchange;
        $this->routingKey = $routingKey;
        $this->mandatory = $mandatory;
        $this->immediate = $immediate;
        $this->beforeMethod = $beforeMethod;
        $this->contentType = $contentType;
        $this->manager = $manager;
    }

    public function publish($message, $routingKey = null, array $headers = [])
    {
        if ($this->beforeMethod) {
            $this->{$this->beforeMethod}($message, $this->manager->getChannel());
        }

        if ($routingKey === null) {
            $routingKey = $this->routingKey;
        }

        $headers["content-type"] = $this->contentType;

        if (!$this->manager->getClient()->isConnected()) {
            try {
                $this->manager->getClient()->connect();
            } catch (\Exception $exception) {
                throw new BunnyException($exception);
            }
        }

        try {
            $this->manager->getChannel()->publish(
                $message,
                $headers,
                $this->exchange,
                $routingKey,
                $this->mandatory,
                $this->immediate
            );
        } catch (\Exception $exception) {
            throw new BunnyException($exception);
        }
    }
}
