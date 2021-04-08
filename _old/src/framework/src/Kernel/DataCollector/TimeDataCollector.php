<?php

declare(strict_types=1);

namespace Tulia\Framework\Kernel\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpKernel\DataCollector\LateDataCollectorInterface;
use Tulia\Framework\Kernel\KernelInterface;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @final
 */
class TimeDataCollector extends DataCollector implements LateDataCollectorInterface
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Throwable $exception = null)
    {
        $this->data = [
            'token' => $response->headers->get('X-Debug-Token'),
            'start_time' => $this->kernel->getStartTime(),
            'duration' => 0,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->data = [];
    }

    /**
     * {@inheritdoc}
     */
    public function lateCollect()
    {
        $this->data['duration'] = microtime(true) - $this->data['start_time'];
    }

    /**
     * Gets the request elapsed time.
     *
     * @return float The elapsed time
     */
    public function getDuration()
    {
        return $this->data['duration'] * 1000;
    }

    /**
     * Gets the request time.
     *
     * @return float
     */
    public function getStartTime()
    {
        return $this->data['start_time'];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'time';
    }
}
