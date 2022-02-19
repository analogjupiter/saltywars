<?php

declare(strict_types=1);

namespace SaltyWars\Launcher;

final class HostedExtension implements ExtensionInterface
{
    private ExtensionInterface $extension;
    private string $path;

    public function __construct(ExtensionInterface $extension, string $path)
    {
        $this->extension = $extension;
        $this->path = $path;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    // --

    public function getName(): string
    {
        return $this->extension->getName();
    }

    public function getVersion(): string
    {
        return $this->extension->getVersion();
    }

    public function getCopyright(): string
    {
        return $this->extension->getCopyright();
    }

    public function bootstrap(): bool
    {
        return $this->extension->bootstrap();
    }

    public function shutdown(): void
    {
        $this->extension->shutdown();
    }
}
