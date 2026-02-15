<?php

namespace App\Services\Ticket\Domain\ValueObjects;

use App\Services\Ticket\Domain\Exceptions\UnknownTicketFormatException;

readonly class TicketFormat {

    private const array KNOWN_TICKET_FORMATS = [
        'e_ticket' => [
            'name' => 'E-ticket',
            'description' => 'An E-Ticket is delivered via app and e-mail. The customer receives a Q.R code they can use to enter the event.'
        ],
        'physical_ticket' => [
            'name' => 'Physical Ticket',
            'description' => 'A Physical ticket is delivered via post or picked up at the door. It contains a Q.R code the customer can use to enter the event.'
        ]
    ];
    private function __construct(
        private string $handle,
        private string $name,
        private string $description,
    ) {
    }

    public function getHandle(): string
    {
        return $this->handle;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @throws UnknownTicketFormatException
     */
    public static function makeFromHandle(string $handle): TicketFormat {
        if (!array_key_exists($handle, self::KNOWN_TICKET_FORMATS)) {
            throw new UnknownTicketFormatException($handle);
        }
        return new static(
            handle: $handle,
            name: self::KNOWN_TICKET_FORMATS[$handle]['name'],
            description: self::KNOWN_TICKET_FORMATS[$handle]['description'],
        );
    }

    /**
     * @throws UnknownTicketFormatException
     */
    public static function getKnownFormats(): array {
        $formats = [];
        foreach (self::KNOWN_TICKET_FORMATS as $handle => $format) {
            $formats[$handle] = self::makeFromHandle($handle);
        }
        return $formats;
    }

    /**
     * @throws UnknownTicketFormatException
     */
    public static function getKnownFormatsForForm(): array {
        $knownFormats = self::getKnownFormats();
        $formatsForForm = [];
        foreach ($knownFormats as $handle => $format) {
            $formatsForForm[$format->getName()] = $format->getHandle();
        }
        return $formatsForForm;
    }

    public static function generateHelpText(): string {
        $helpString  = '<ul>';
        foreach (self::getKnownFormats() as $handle => $format) {
            $helpString .= sprintf("<li>%s</li>", $format->getDescription());
        }
        $helpString .= '</ul>';
        $helpString.='<p>Each Ticket Format enabled will generate a separate product for said format.</p>';
        return $helpString;
    }
}
