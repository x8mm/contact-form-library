<?php

declare(strict_types=1);

namespace ContactForm\Config;

use ContactForm\Exception\ConfigurationException;

/**
 * Zentrale Konfigurationsklasse.
 *
 * Diese Klasse kapselt sämtliche Zugriffe auf die aus der .env-Datei
 * geladenen Umgebungsvariablen. Alle Komponenten der Anwendung greifen
 * ausschließlich über diese Klasse auf Konfigurationswerte zu.
 */
final class Config
{
    /**
     * Zwischenspeicher bereits gelesener Werte.
     *
     * @var array<string, mixed>
     */
    private array $cache = [];

    /**
     * Liefert einen String.
     *
     * @throws ConfigurationException
     */
    public function getString(string $key): string
    {
        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }

        $value = $_ENV[$key] ?? null;

        if ($value === null) {
            throw new ConfigurationException(
                sprintf('Konfigurationswert "%s" wurde nicht gefunden.', $key)
            );
        }

        $value = trim((string) $value);

        $this->cache[$key] = $value;

        return $value;
    }

    /**
     * Liefert einen String oder einen Standardwert.
     */
    public function getStringOrDefault(string $key, string $default): string
    {
        $value = $_ENV[$key] ?? null;

        if ($value === null || trim((string) $value) === '') {
            return $default;
        }

        return trim((string) $value);
    }

    /**
     * Liefert einen Integer.
     *
     * @throws ConfigurationException
     */
    public function getInt(string $key): int
    {
        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }

        $value = $this->getString($key);

        if (!is_numeric($value)) {
            throw new ConfigurationException(
                sprintf(
                    'Konfigurationswert "%s" muss eine Ganzzahl sein.',
                    $key
                )
            );
        }

        $integer = (int) $value;

        $this->cache[$key] = $integer;

        return $integer;
    }

    /**
     * Liefert einen Boolean.
     *
     * Unterstützt:
     * true
     * false
     * 1
     * 0
     * yes
     * no
     * on
     * off
     *
     * @throws ConfigurationException
     */
    public function getBool(string $key): bool
    {
        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }

        $value = filter_var(
            $this->getString($key),
            FILTER_VALIDATE_BOOLEAN,
            FILTER_NULL_ON_FAILURE
        );

        if ($value === null) {
            throw new ConfigurationException(
                sprintf(
                    'Konfigurationswert "%s" ist kein gültiger Boolean.',
                    $key
                )
            );
        }

        $this->cache[$key] = $value;

        return $value;
    }

    /**
     * Liefert eine Liste aus einer komma-separierten Variable.
     *
     * Beispiel:
     *
     * MAIL_RECIPIENTS=a@example.com,b@example.com
     *
     * @return array<int, string>
     */
    public function getArray(string $key): array
    {
        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }

        $value = $this->getString($key);

        $items = array_values(
            array_filter(
                array_map(
                    static fn (string $item): string => trim($item),
                    explode(',', $value)
                ),
                static fn (string $item): bool => $item !== ''
            )
        );

        $this->cache[$key] = $items;

        return $items;
    }

    /**
     * Prüft, ob ein Konfigurationswert vorhanden ist.
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $_ENV);
    }

    /**
     * Liefert alle geladenen Konfigurationswerte.
     *
     * @return array<string, string>
     */
    public function all(): array
    {
        /** @var array<string, string> $env */
        $env = $_ENV;

        return $env;
    }

    /**
     * Leert den internen Cache.
     */
    public function clearCache(): void
    {
        $this->cache = [];
    }
}