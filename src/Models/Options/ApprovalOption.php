<?php

namespace AssistantEngine\SDK\Models\Options;

class ApprovalOption
{
    public int $tool_call_id;
    public ?string $message;
    public string $type;

    public function __construct(int $toolCallId, string $type, ?string $message = null)
    {
        $this->tool_call_id = $toolCallId;
        $this->message = $message;
        $this->type = $type;
    }

    public function toArray(): array
    {
        return [
            'tool_call_id' => $this->tool_call_id,
            'message' => $this->message,
            'type' => $this->type,
        ];
    }
}