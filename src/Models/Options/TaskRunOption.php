<?php

namespace AssistantEngine\SDK\Models\Options;

class TaskRunOption
{
    /** @var array The context data for the task run */
    public array $context;

    /**
     * TaskRunOption constructor.
     *
     * @param array $context The context data to initialize the task run option.
     */
    public function __construct(array $context)
    {
        $this->context = $context;
    }

    /**
     * Convert the object to an associative array.
     *
     * @return array The array representation of the object.
     */
    public function toArray(): array
    {
        return [
            'context' => $this->context,
        ];
    }
}
