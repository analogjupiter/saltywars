<?php

declare(strict_types=1);

namespace SaltyWars\Launcher;

use Exception;

class ExtensionHost
{
    const EXTENSION_INFO_FILE = 'xt.php';

    /** @var array<HostedExtension> */
    private array $extensions;

    public function __construct()
    {
    }

    /** @throws */
    public function loadExtensions(string $extensionsDirPath): void
    {
        $dir = new \DirectoryIterator($extensionsDirPath);

        foreach ($dir as $file) {
            if (!$file->isDir()) {
                continue;
            }

            $xtPath = $file->getPathname() . '/' . self::EXTENSION_INFO_FILE;
            if (!file_exists($xtPath)) {
                continue;
            }

            $xtCodeName = $file->getFilename() . 'Extension';
            require $xtPath;

            if (!class_exists($xtCodeName)) {
                throw new Exception('Faulty extension: ' . $xtPath);
            }
            if (!in_array(ExtensionInterface::class, class_implements($xtCodeName))) {
                throw new Exception('Faulty extension does not implement ExtensionInterface: ' . $xtPath);
            }

            $xt = new $xtCodeName();
            $this->extensions[] = new HostedExtension($xt, $xtPath);
        }
    }

    /**
     * @throws Exception
     */
    public function bootstrap(): void
    {
        foreach ($this->extensions as $xt) {
            $r = $xt->bootstrap();
            if (!$r) {
                throw new Exception ('Failed to bootstrap extension: ' . $xt->getPath());
            }
        }
    }
}
