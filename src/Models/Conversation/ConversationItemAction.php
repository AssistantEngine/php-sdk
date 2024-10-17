<?php

namespace AssistantEngine\SDK\Models\Conversation;

class ConversationItemAction
{
    /** @var string */
    public const ROLE_TOOL = 'tool';

    // Status constants
    public const STATUS_PENDING = 'pending';
    public const STATUS_ERROR = 'error';
    public const STATUS_SUCCESS = 'success';

    /** @var string */
    public string $id;

    /** @var string */
    public string $role;

    /** @var string */
    public string $content;

    /** @var string|null */
    public ?string $status = null;

    /**
     * ConversationItemAction constructor.
     *
     * @param array $data The data to populate the action.
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->role = $data['role'];
        $this->content = $data['content'];
        $this->status = $data['status'] ?? null;
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
            'role' => $this->role,
            'content' => $this->content,
            'status' => $this->status,
        ];
    }
}
