<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Listener\HttpViewListener;
use App\Domain\Messenger\TemplateStamp;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Serializer\SerializerInterface;

class ViewListenerTest extends Unit
{
    private HttpViewListener $viewListener;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $serializer = $this->createMock(originalClassName: SerializerInterface::class);
        $this->viewListener = new HttpViewListener($serializer);
    }

    /**
     * @throws Exception
     */
    public function testInvokeWithTemplateStamp(): void
    {
        $viewEvent = new ViewEvent(
            kernel: $this->createMock(originalClassName: HttpKernelInterface::class),
            request: $this->createMock(originalClassName: Request::class),
            requestType: HttpKernelInterface::MAIN_REQUEST,
            controllerResult: new Envelope(new stdClass(), [
                new TemplateStamp(template: 'example.html.twig'),
            ]),
            controllerArgumentsEvent: null,
        );

        ($this->viewListener)($viewEvent);
    }
}
