<?php

namespace AssistantEngine\SDK\Models\Conversation;

class Conversation
{
    // Conversation status constants
    public const STATUS_INITIALIZING = 'initializing';
    public const STATUS_THINKING = 'thinking';
    public const STATUS_TYPING = 'typing';
    public const STATUS_EXECUTING = 'executing';
    public const STATUS_ERROR = 'error';
    public const STATUS_CANCELLING = 'cancelling';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_COMPLETE = 'complete';

    // Conversation properties
    public int $id;
    public bool $is_active;
    public ?string $user_id;
    public ?string $title;
    public array $context = [];
    public array $additional_data = [];
    public ?string $subject_id;
    public string $assistant_key;
    public ?string $last_run_status = null;
    public ?bool $last_run_in_finite_state = null;
    public ?string $error_message = null;

    /** @var ConversationItem[] */
    public array $pending_items = [];

    /** @var ConversationItem[] */
    public array $history = [];

    /**
     * Conversation constructor.
     * @param array $data Array to initialize the conversation.
     */
    public function __construct(array $data)
    {
        $this->fromArray($data);
    }

    /**
     * Populate object properties from an array.
     * @param array $data
     */
    public function fromArray(array $data): void
    {
        $this->id = $data['id'];
        $this->is_active = $data['is_active'];
        $this->user_id = $data['user_id'] ?? null;
        $this->title = $data['title'] ?? null;
        $this->context = $data['context'] ?? [];
        $this->additional_data = $data['additional_data'] ?? [];
        $this->subject_id = $data['subject_id'] ?? null;
        $this->assistant_key = $data['assistant_key'];

        // Last run data
        if (isset($data['last_run'])) {
            $this->last_run_status = $data['last_run']['status'] ?? null;
            $this->last_run_in_finite_state = $data['last_run']['in_finite_state'] ?? null;
        }

        // Error data
        if (isset($data['error'])) {
            $this->error_message = $data['error']['message'] ?? null;
        }

        // Pending items and history
        $this->pending_items = array_map(fn($item) => new ConversationItem($item), $data['pending_items'] ?? []);
        $this->history = array_map(fn($item) => new ConversationItem($item), $data['history'] ?? []);
    }

    /**
     * Convert the object to an array.
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'is_active' => $this->is_active,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'context' => $this->context,
            'additional_data' => $this->additional_data,
            'subject_id' => $this->subject_id,
            'assistant_key' => $this->assistant_key,
            'last_run' => [
                'status' => $this->last_run_status,
                'in_finite_state' => $this->last_run_in_finite_state
            ],
            'error' => [
                'message' => $this->error_message
            ],
            'pending_items' => $this->pendingItemsAsArray(),
            'history' => $this->historyAsArray()
        ];
    }

    /**
     * Convert pending items to array.
     * @return array
     */
    public function pendingItemsAsArray(): array
    {
        return array_map(fn($item) => $item->toArray(), $this->pending_items);
    }

    /**
     * Convert history items to array.
     * @return array
     */
    public function historyAsArray(): array
    {
        return array_map(fn($item) => $item->toArray(), $this->history);
    }

    /**
     * Count total messages in the history.
     * @return int
     */
    public function countHistoryMessages(): int
    {
        return array_sum(array_map(fn($item) => count($item->messages), $this->history));
    }

    /**
     * Count total pending action items which are required actions.
     * @return int
     */
    public function countPendingRequiredActions(): int
    {
        $count = 0;
        foreach ($this->pending_items as $item) {
            foreach ($item->actions as $action) {
                if ($action->status === ConversationItemAction::STATUS_REQUIRES_CONFIRMATION) {
                    $count++;
                }
            }
        }
        return $count;
    }

    /**
     * Count total messages in pending items.
     * @return int
     */
    public function countPendingMessages(): int
    {
        return array_sum(array_map(fn($item) => count($item->messages), $this->pending_items));
    }

    /**
     * Count total messages (history + pending).
     * @return int
     */
    public function countTotalMessages(): int
    {
        return $this->countHistoryMessages() + $this->countPendingMessages() + $this->countPendingRequiredActions();
    }

    /**
     * Get the last pending item by role.
     * @param string $role
     * @return ConversationItem|null
     */
    public function getPendingItemByRole(string $role): ?ConversationItem
    {
        foreach (array_reverse($this->pending_items) as $item) {
            if ($item->role === $role) {
                return $item;
            }
        }
        return null;
    }

    /**
     * Get the last conversation item by role.
     * @param string $role
     * @return ConversationItem|null
     */
    public function getLastConversationItemByRole(string $role): ?ConversationItem
    {
        foreach (array_reverse($this->history) as $item) {
            if ($item->role === $role) {
                return $item;
            }
        }
        return null;
    }

    /**
     * Check if the conversation is in a finite state.
     * @return bool
     */
    public function isInFiniteState(): bool
    {
        return (bool) $this->last_run_in_finite_state;
    }
}
