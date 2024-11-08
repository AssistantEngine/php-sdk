
# Assistant Engine PHP SDK

The [Assistant Engine](https://www.assistant-engine.com/) PHP SDK provides a convenient way to interact with the Assistant Engine API, allowing developers to create and manage conversations, tasks, and messages.

## Requirements

- PHP 8.2 or higher
- Composer
- OpenAI API Key (See [OpenAI Documentation](https://platform.openai.com/docs/api-reference/authentication))
- Assistant Engine API Key (See [Assistant Engine Documentation](https://docs.assistant-engine.com/docs/projects#api-key))

> **Important Notice**: Both an OpenAI API key and an Assistant Engine API key are required to use this SDK. Make sure these keys are configured correctly by following the respective documentation linked above.

## Documentation

Official documentation for the Assistant Engine PHP SDK can be found on the [Assistant Engine Documentation](https://docs.assistant-engine.com/docs) website.

## Installation

You can install the Assistant Engine PHP SDK via Composer:

```bash
composer require assistant-engine/php-sdk
```

## Usage

### Initialization

To start using the SDK, initialize the `AssistantEngine` class with your API URL, API token and OpenAI token.

```php
use AssistantEngine\SDK\AssistantEngine;

$assistantEngine = new AssistantEngine(
    'https://api.assistant-engine.com/v1/',     // API URL
    'your_api_token',                           // API Token
    'your_openai_token',                        // OpenAI Token
);
```

### Conversation Management

#### Create or Find a Conversation

To create or find a conversation, use the `findOrCreateConversation` method with a `ConversationOption` object:

```php
use AssistantEngine\SDK\Models\Options\ConversationOption;

$options = new ConversationOption('assistant_key', [
    'user_id' => 'user123',
    'title' => 'New Conversation',
    'context' => ['key' => 'value'],
    'additional_data' => ['theme' => 'dark']
]);

$conversation = $assistantEngine->findOrCreateConversation($options);
```

**ConversationOption** represents the settings used to create or find a conversation and includes the following options:
- **assistant_key** (required): Unique key identifying the assistant.
- **user_id** (optional): ID of the user associated with the conversation, allowing multiple users to have different conversations with the same assistant.
- **subject_id** (optional): ID of a specific subject, enabling a user to have separate conversations with the assistant about different topics.
- **title** (optional): Title of the conversation, used solely for client purposes.
- **context** (optional): Arbitrary data to provide context to the conversation. This context is included with the conversation data sent to the LLM.
- **additional_data** (optional): Data intended for the front-end or client application, allowing additional operations based on its content.
- **recreate** (optional): If set to true, recreates the conversation, deactivating the previous one.

> Note: The Assistant Engine will attempt to locate an existing conversation based on the combination of assistant_key, user_id, and subject_id. If a match is found, that conversation will be retrieved; otherwise, a new one will be created.

#### Retrieve a Conversation by ID

To retrieve a specific conversation:

```php
$conversation = $assistantEngine->getConversation($conversationId);
```

**Conversation** represents a conversation session and includes properties such as:
- **id**: Unique identifier of the conversation.
- **is_active**: Boolean indicating if the conversation is active.
- **user_id**: ID of the user associated with the conversation.
- **title**: Title of the conversation.
- **context**: Contextual data relevant to the conversation.
- **additional_data**: Additional data relevant to the conversation.
- **subject_id**: ID of the subject associated with the conversation.
- **assistant_key**: The key for the assistant managing the conversation.
- **last_run_status**: Status of the last run in the conversation.
- **error_message**: Any error message if the conversation encountered an issue.

**Conversation Item**: Represents individual messages, actions, or events within the conversation, such as user messages or assistant responses. Each item can be:
- **Pending**: Indicates that the LLM is still processing and the item isn’t in a finite state.
- **Completed**: The LLM has processed the item, and it has reached a finite state.

**Run**: When a request is made to the LLM, it is called a "run." During a run, items may be in a pending state. Once the run completes and reaches a finite state, the pending items become finalized. Until a run reaches a finite state, additional actions such as recreating or re-triggering the run cannot occur.

Available methods for the `Conversation` object include:
- `toArray()`: Convert the object to an associative array.
- `countTotalMessages()`: Count the total number of messages in both history and pending items.
- `countHistoryMessages()`: Count the messages in the conversation history.
- `countPendingMessages()`: Count the pending messages.
- `getPendingItemByRole($role)`: Retrieve the last pending item by a specific role.
- `getLastConversationItemByRole($role)`: Retrieve the last item in the conversation history by role.
- `isInFiniteState()`: Check if the conversation has reached a finite state.

#### Update a Conversation

To update a conversation's title, context, or additional data:

```php
use AssistantEngine\SDK\Models\Options\ConversationUpdateOption;

$updateOptions = new ConversationUpdateOption();
$updateOptions->title = 'Updated Title';
$updateOptions->context = ['key' => 'value'];
$updateOptions->additional_data = ['theme' => 'dark'];

$updatedConversation = $assistantEngine->updateConversation($conversationId, $updateOptions);
```

#### Deactivate a Conversation

To deactivate a conversation:

```php
$response = $assistantEngine->deactivateConversation($conversationId);
```

### Messages

#### Create a Message in a Conversation

To send a message within an existing conversation:

```php
use AssistantEngine\SDK\Models\Options\MessageOption;

$messageOption = new MessageOption();
$messageOption->message = 'Hello, Assistant!';

$response = $assistantEngine->createMessage($conversation->id, $messageOption);
```

### Task Management

#### What is a Task?

A **task** is a defined operation that the LLM should execute. Tasks can be configured in the Assistant Engine, including specifications on required context. Read more in the [Assistant Engine Task Documentation](https://docs.assistant-engine.com/docs/Tasks). 

Required context must be provided in the `TaskRunOption`; otherwise, an exception will be thrown. This context will be included in the LLM message if marked as required, while other context data will be added to the general instructions.

#### Initiate a Task Run

To start a task with specific context data:

```php
use AssistantEngine\SDK\Models\Options\TaskRunOption;

$taskRunOption = new TaskRunOption(['key' => 'value']);
$taskRunResponse = $assistantEngine->initiateTaskRun('task_key', $taskRunOption);
```

#### Poll Task Run Until Complete

To continuously check the status of a task run until it completes:

```php
$taskOutput = $assistantEngine->pollTaskRunUntilComplete('task_key', $runId);
```

#### Initiate Task Run and Poll

To initiate a task and automatically poll until it's complete:

```php
$taskOutput = $assistantEngine->initiateTaskRunAndPoll('task_key', $taskRunOption);
```

**Task Output**

The TaskOutput object represents the result of a task execution. Here’s what it looks like:
- **id**: The unique identifier of the task output.
- **task_id**: The associated task ID.
- **is_running**: Boolean indicating if the task is still running.
- **output**: The final output of the task once it is complete.

Example usage to retrieve task output:

```php
$taskOutput = $assistantEngine->getTaskRun('task_key', $runId);
if (!$taskOutput->is_running) {
    echo "Task completed with output: " . $taskOutput->output;
}
```

## One more thing

We’ve created more repositories to make working with the Assistant Engine even easier! Check them out:

- **[Laravel Assistant](https://github.com/AssistantEngine/laravel-assistant)**: A library with Livewire components and an Assistant command to easily integrate the Assistant Engine as a Livewire or console chat within Laravel applications.
- **[Filament Assistant](https://github.com/AssistantEngine/filament-assistant)**: A plugin for Laravel Filament that adds a chat assistant in the Filament panel, along with automatic context injection from resources and task execution buttons.

> We are a young startup aiming to make it easy for developers to add AI to their applications. Feedback, questions, comments, and PRs are welcome. Reach out at [contact@assistant-engine.com](mailto:contact@assistant-engine.com).

## License

The MIT License (MIT). Please see [License File](license.md) for more information.
