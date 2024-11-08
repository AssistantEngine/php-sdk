<?php

namespace AssistantEngine\SDK\Models\Conversation;

class ConversationItemMessage
{
    /** @var string */
    public string $id;

    /** @var string */
    public string $content;

    /**
     * ConversationItemMessage constructor.
     *
     * @param array $data The data to initialize the message.
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->content = $data['content'];
    }

    /**
     * Convert the object to an associative array.
     *
     * @return array The array representation of the object.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
        ];
    }
}
