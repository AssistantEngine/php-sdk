<?php

namespace AssistantEngine\SDK\Models\Options;

class MessageOption
{
    /** @var string The message content */
    public string $message;

    /**
     * Convert the object to an associative array.
     *
     * @return array The array representation of the object.
     */
    public function toArray(): array
    {
        return [
            'message' => $this->message,
        ];
    }
}
