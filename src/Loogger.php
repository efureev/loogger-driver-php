<?php

declare(strict_types=1);

namespace Fureev\Loogger;

use RuntimeException;

use function curl_init;
use function curl_setopt;
use function curl_exec;
use function curl_close;

final class Loogger
{
    /** @var string[] */
    private array $errors = [];

    /**
     * @var ?string
     * @see Config::$pattern
     */
    private ?string $richFormat;

    private string $contentType = 'text/plain';

    /**
     * @var ?string
     * @see Config::$pattern
     */
    private ?string $pattern;


    public function __construct(private Config $config)
    {
        $this->pattern    = $config->pattern;
        $this->richFormat = $config->richFormat;
    }

    public function patternToDefault(): self
    {
        $this->pattern = null;

        return $this;
    }

    public function pattern(string $str): self
    {
        $this->pattern = $str;

        return $this;
    }

    public function debug(): self
    {
        $this->config->setDebug();

        return $this;
    }

    private function toDebugLog(string $str): self
    {
        if ($this->config->debug) {
            echo "$str\n";
        }

        return $this;
    }

    public function asHTML(): self
    {
        $this->richFormat = 'html';

        return $this;
    }

    public function asPlain(): self
    {
        $this->richFormat = 'plain';

        return $this;
    }

    public function asMD(): self
    {
        $this->richFormat = 'md';

        return $this;
    }

    private function buildHeaders(): array
    {
        return array_merge(
            [
                "X-Token: {$this->config->token}",
                "Content-Type: $this->contentType",
                'Accept: application/json',
                'Accept-Encoding: deflate, br',
            ],
            $this->pattern ? ["X-Pattern: $this->pattern",] : [],
            $this->richFormat ? ["X-Rich-Formatting: $this->richFormat",] : [],
        );
    }

    public function send(string $message): array
    {
        if ($this->curl($message)->hasErrors()) {
            return $this->errors;
        }

        return [];
    }

    public function sendHTML(string $message): array
    {
        return $this->asHTML()->send($message);
    }

    public function sendDebug(string $message): void
    {
        if ($this->config->debug) {
            $this->send($message);
        }
    }

    private function curl(string $message): self
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url = "{$this->config->host}/push");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $message);

        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->buildHeaders());

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);

        $this
            ->toDebugLog("\nURL: $url")
            ->toDebugLog("HEADERS: " . print_r($this->buildHeaders(), true))
            ->toDebugLog("DATA: " . print_r($message, true));

        $result = curl_exec($ch);

        $this->toDebugLog("------[\nRESPONSE: $result\n]------\n");

        try {
            if ($errno = curl_errno($ch)) {
                throw new RuntimeException('curl error: ' . $errno);
            }

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $this->toDebugLog("HTTP CODE: $httpCode");
            if ($httpCode !== 202) {
                throw new RuntimeException('invalid http code: ' . $httpCode);
            }
            $this->toDebugLog("curl sent successfully\nMESSAGE: " . $message);
        } catch (RuntimeException $exception) {
            $this->toDebugLog("[ERR] " . $exception->getMessage());
            $this->addError($exception->getMessage());
        } finally {
            curl_close($ch);
        }

        return $this;
    }

    public function addError(string $error): self
    {
        $this->errors[] = $error;

        return $this;
    }

    public function hasErrors(): bool
    {
        return count($this->errors) > 0;
    }
}
