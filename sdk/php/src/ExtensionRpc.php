<?php

declare(strict_types=1);

namespace SaltyWars\Sdk;

final class ExtensionRpc
{
    private const LF = "\n";

    private $stdout;
    private $stderr;
    private $stdin;
    private bool $running = false;

    private $dispatchCall;

    public function __construct(callable $dispatchCall)
    {
        $this->stdout = fopen('php://stdout', 'w');
        $this->stderr = fopen('php://stderr', 'w');
        $this->stdin = fopen('php://stdin', 'r');
     
        $this->dispatchCall = $dispatchCall;
    }

    public function __destruct()
    {
        fclose($this->stdout);
        fclose($this->stderr);
    }

    public function run(): void
    {
        // self-test
        if ($this->running) {
            throw new \LogicException('RPC is already running');
        }

        $this->running = true;
        try {
            $this->runImpl();
        } finally {
            $this->running = false;
        }
    }

    public function call(string $function, ...$params): array
    {
        $this->sendCall($function, $params);
        return $this->receiveRetDispatch();
    }

    private function runImpl(): void
    {
        // init
        $this->send('saltywars/rpc-compatible/json');

        // loop
        while(true) {
            $msg = $this->receive();
            $this->dispatchExec($msg['f'], $msg['p']);
        }
    }

    private function receiveRetDispatch(): array
    {
        do {
            $msg = $this->receive();

            // call (not a return)?
            if ($msg['f'] !== 'return') {
                $this->dispatchExec($msg['f'], $msg['p']);
            }

            // received "return"
            return $msg['p'];
        } while (true);
    }

    private function dispatchExec(string $f, array $p): void
    {
        $callable = $this->dispatch($f);
        try {
            $result = $callable($this, $p);
        } catch (Exception $ex) {
            // TODO
            throw $ex;
        }

        $this->ret($result);
    }


    private function dispatch(string $functionName): callable
    {
        return $this->dispatchCall($msg['f']);
    }


    private function ret($result): void
    {
        $this->call('return', $result);
    }

    private function sendCall(string $function, array $params): void
    {
        $this->send(
            json_encode(
                [
                    'f' => $function,
                    'p' => $params,
                ],
                JSON_THROW_ON_ERROR,
            ),
        );
    }

    private function send(string $line): void
    {
        fwrite($this->stdout, $line);
        fwrite($this->stdout, self::LF);
    }

    private function receive(): array
    {
        $line = fgets($this->stdin);
        if ($line === false) {
            // TODO
            throw new \Exception('Broken pipe');
        }

        $msg = json_decode(
            $line,
            true,
        );
        
        // validate
        $valid = (
            is_array($msg)
            && isset($msg['f'])
            && is_string($msg['f'])
            && isset($msg['p'])
            && is_array($msg['p'])
        );

        if (!$valid) {
            // TODO
            throw new \Exception('Received invalid message');
        }

        return $msg;
    }
}