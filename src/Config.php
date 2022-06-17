<?php

declare(strict_types=1);

namespace Fureev\Loogger;

use function property_exists;
use InvalidArgumentException;

/**
 * @property-read string $token
 * @property-read string $host
 * @property-read string $richFormat
 * @property-read ?string $pattern
 * @property-read boolean $debug
 */
final class Config
{
    private string $token;

    private string $host;

    private bool $debug = false;

    /**
     * Rich formatting for the message.
     *
     * Allowed values:
     * - `plain` - For a plain text. **By default**
     * - `md` - For formatting message by Markdown
     * - `html` - For formatting message by HTML
     *
     * @var string
     */
    private string $richFormat;

    /**
     * The message template that will be shown in the target.
     *
     * Allowed variables:
     * - `MSG` - The Message content
     * - `SERVICE_NAME` - The service name
     * - `SERVICE_DESCRIPTION` - The service description
     * - `NOW` - The datetime. Format: `RFC822`
     * - `CHAT_ID` - The TG-chat ID
     * - `BR` - Return a cursor to the next line. Like a `\n`.
     * - `TAB` - Return a cursor to the next line. Like a `\t`.
     * By default: `[{{SERVICE_NAME}}] {{MSG}}`
     * @var ?string
     */
    private ?string $pattern;

    public function __construct(array $params)
    {
        foreach ($params as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function __get(string $name): mixed
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        throw new InvalidArgumentException("Property '$name' is missing");
    }

    public function setDebug(bool $enable = true): void
    {
        $this->debug = $enable;
    }
}
