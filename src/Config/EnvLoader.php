<?php

declare(strict_types=1);

namespace ContactForm\Config;

use ContactForm\Exception\ConfigurationException;
use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;

/**
 * Lädt und validiert die Projektkonfiguration aus der .env-Datei.
 *
 * Diese Klasse kapselt den Zugriff auf vlucas/phpdotenv vollständig.
 * Andere Komponenten des Projekts greifen niemals direkt auf $_ENV
 * oder getenv() zu.
 */
final class EnvLoader
{
    /**
     * Verzeichnis, das die .env-Datei enthält.
     */
    private readonly string $projectRoot;

    /**
     * Gibt an, ob die Konfiguration bereits geladen wurde.
     */
    private bool $loaded = false;

    /**
     * @param string $projectRoot Projektwurzel (enthält die .env-Datei)
     */
    public function __construct(string $projectRoot)
    {
        $this->projectRoot = rtrim($projectRoot, DIRECTORY_SEPARATOR);
    }

    /**
     * Lädt die .env-Datei.
     *
     * @throws ConfigurationException
     */
    public function load(): void
    {
        if ($this->loaded) {
            return;
        }

        try {
            $dotenv = Dotenv::createImmutable($this->projectRoot);
            $dotenv->load();
        } catch (InvalidPathException $exception) {
            throw new ConfigurationException(
                'Die Datei ".env" wurde nicht gefunden.',
                previous: $exception
            );
        }

        $this->loaded = true;
    }

    /**
     * Prüft, ob alle erforderlichen Variablen vorhanden sind.
     *
     * @param array<int,string> $variables
     *
     * @throws ConfigurationException
     */
    public function require(array $variables): void
    {
        foreach ($variables as $variable) {
            $value = $_ENV[$variable] ?? null;

            if ($value === null || trim((string) $value) === '') {
                throw new ConfigurationException(
                    sprintf(
                        'Die erforderliche Umgebungsvariable "%s" fehlt.',
                        $variable
                    )
                );
            }
        }
    }

    /**
     * Prüft, ob die Konfiguration bereits geladen wurde.
     */
    public function isLoaded(): bool
    {
        return $this->loaded;
    }
}