<?php

declare(strict_types=1);

namespace SaltyWars\Launcher;

interface ExtensionInterface
{
    function getName(): string;
    function getVersion(): string;
    function getCopyright(): string;
    function bootstrap(): bool;
    function shutdown(): void;
}