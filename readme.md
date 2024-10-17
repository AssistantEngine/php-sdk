# Assistant Engine PHP SDK

PHP SDK for seamless integration with the Assistant Engine, a SaaS solution for managing assistants, tasks, and projects.

## Installation

```bash
composer require assistant-engine/php-sdk
```

```php
<?php

use AssistantEngine\SDK\AssistantEngine;
use AssistantEngine\SDK\Models\Options\ConversationOption;

$apiUrl = 'https://api.assistant-engine.com/v1';
$apiKey = 'your_assistant_engine_token';
$openAIToken = 'your_openai_token';

$assistantEngine = new AssistantEngine($apiUrl, $apiKey, $openAIToken);

$conversationOption = new ConversationOption("your-assistant-key");
```

### License
This project is licensed under the MIT License 