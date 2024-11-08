<?php

namespace AssistantEngine\SDK\Models\Options;

class ConversationUpdateOption
{
    /** @var string|null The title of the conversation, if updated */
    public ?string $title = null;

    /** @var array The updated context of the conversation */
    public array $context = [];

    /** @var array Any additional data for the conversation */
    public array $additional_data = [];

    /**
     * Convert the object to an associative array.
     *
     * @return array The array representation of the object.
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'context' => $this->context,
            'additional_data' => $this->additional_data,
        ];
    }
}
