<?php

namespace AssistantEngine\SDK\Models\Options;

class ConversationOption
{
    /** @var string */
    public string $assistant_key;

    /** @var string|null */
    public ?string $user_id = null;

    /** @var string|null */
    public ?string $subject_id = null;

    /** @var string|null */
    public ?string $title = null;

    /** @var array */
    public array $context = [];

    /** @var array */
    public array $additional_data = [];

    /** @var bool|null */
    public ?bool $recreate = null;

    /**
     * ConversationOption constructor.
     *
     * @param string $assistant_key
     * @param array $options
     */
    public function __construct(string $assistant_key, array $options = [])
    {
        $this->assistant_key = $assistant_key;

        // Optional values, set if provided in the options array
        $this->user_id = $options['user_id'] ?? null;
        $this->subject_id = $options['subject_id'] ?? null;
        $this->title = $options['title'] ?? null;
        $this->context = $options['context'] ?? [];
        $this->additional_data = $options['additional_data'] ?? [];
        $this->recreate = $options['recreate'] ?? null;
    }

    /**
     * Convert the object to an associative array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'assistant_key' => $this->assistant_key,
            'user_id' => $this->user_id,
            'subject_id' => $this->subject_id,
            'title' => $this->title,
            'context' => $this->context,
            'additional_data' => $this->additional_data,
            'recreate' => $this->recreate,
        ];
    }
}
