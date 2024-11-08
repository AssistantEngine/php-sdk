<?php

namespace AssistantEngine\SDK\Models\Task;

class TaskOutput
{
    /** @var int The ID of the task output */
    public int $id;

    /** @var int The associated task ID */
    public int $task_id;

    /** @var bool Whether the task is currently running */
    public bool $is_running;

    /** @var string The output of the task */
    public string $output;

    /**
     * TaskOutput constructor.
     *
     * @param array $data The data to initialize the task output.
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->task_id = $data['task_id'];
        $this->is_running = $data['is_running'];
        $this->output = $data['output'];
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
            'task_id' => $this->task_id,
            'is_running' => $this->is_running,
            'output' => $this->output,
        ];
    }
}
