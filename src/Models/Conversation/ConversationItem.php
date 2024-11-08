<?php

namespace AssistantEngine\SDK\Models\Conversation;

class ConversationItem
{
    // Role constants
    public const ROLE_USER = 'user';
    public const ROLE_ASSISTANT = 'assistant';
    public const ROLE_ERROR = 'error';

    /** @var string|null The role in the conversation */
    public ?string $role;

    /** @var string|null The status of the run */
    public ?string $run_status = "";

    /** @var ConversationItemMessage[] Array of message objects */
    public array $messages = [];

    /** @var ConversationItemAction[] Array of action objects */
    public array $actions = [];

    /**
     * ConversationItem constructor.
     * @param array $data The data to populate the ConversationItem.
     */
    public function __construct(array $data)
    {
        $this->role = $data['role'] ?? null;
        $this->run_status = $data['run_status'] ?? "";

        // Map messages if they exist
        $this->messages = isset($data['messages'])
            ? array_map(fn($message) => new ConversationItemMessage($message), $data['messages'])
            : [];

        // Map actions if they exist
        $this->actions = isset($data['actions'])
            ? array_map(fn($action) => new ConversationItemAction($action), $data['actions'])
            : [];
    }

    /**
     * Convert the object to an array.
     * @return array The array representation of the object.
     */
    public function toArray(): array
    {
        return [
            'role' => $this->role,
            'run_status' => $this->run_status,
            'messages' => array_map(fn($message) => $message->toArray(), $this->messages),
            'actions' => array_map(fn($action) => $action->toArray(), $this->actions),
        ];
    }
}
