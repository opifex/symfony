<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Listener\ViewEventListener;
use App\Application\Messenger\TemplateStamp;
use Codeception\Test\Unit;
use Override;
use PHPUnit\Framework\MockObject\Exception;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Serializer\SerializerInterface;

class ViewEventListenerTest extends Unit
{
    /**
     * @throws Exception
     */
    #[Override]
    protected function setUp(): void
    {
        $this->httpKernel = $this->createMock(originalClassName: HttpKernelInterface::class);
        $this->request = $this->createMock(originalClassName: Request::class);
        $this->serializer = $this->createMock(originalClassName: SerializerInterface::class);
    }

    /**
     * @throws Exception
     */
    public function testInvokeWithTemplateStamp(): void
    {
        $viewEventListener = new ViewEventListener($this->serializer);
        $viewEvent = new ViewEvent(
            kernel: $this->httpKernel,
            request: $this->request,
            requestType: HttpKernelInterface::MAIN_REQUEST,
            controllerResult: new Envelope(new stdClass(), [
                new TemplateStamp(template: 'example.html.twig'),
            ]),
            controllerArgumentsEvent: null,
        );

        ($viewEventListener)($viewEvent);

        $this->expectNotToPerformAssertions();
    }
}
