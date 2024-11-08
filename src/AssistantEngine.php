<?php

namespace AssistantEngine\SDK;

use AssistantEngine\SDK\Models\Conversation\Conversation;
use AssistantEngine\SDK\Models\Options\ConversationOption;
use AssistantEngine\SDK\Models\Options\ConversationUpdateOption;
use AssistantEngine\SDK\Models\Options\MessageOption;
use AssistantEngine\SDK\Models\Options\TaskRunOption;
use AssistantEngine\SDK\Models\Task\TaskOutput;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class AssistantEngine
{
    protected string $apiUrl;
    protected string $apiToken;
    protected string $openAIToken;
    protected ?string $basicAuth = null; // Optional Basic Auth
    protected Client $client;

    /**
     * Constructor for the AssistantEngine class.
     * Ensures that the API URL ends with a trailing slash.
     *
     * @param string $apiUrl
     * @param string $apiToken
     * @param string $openAIToken
     * @param string|null $basicAuth
     */
    public function __construct(string $apiUrl, string $apiToken, string $openAIToken, ?string $basicAuth = null)
    {
        // Ensure API URL has a trailing slash
        $this->apiUrl = rtrim($apiUrl, '/') . '/';
        $this->apiToken = $apiToken;
        $this->openAIToken = $openAIToken;
        $this->basicAuth = $basicAuth;

        $this->initializeClient();
    }

    /**
     * Initializes the Guzzle client with the appropriate headers.
     */
    protected function initializeClient(): void
    {
        $authorizationHeader = '';
        // Append Basic Auth if provided
        if ($this->basicAuth) {
            $authorizationHeader = 'Basic ' . $this->basicAuth . ", ";
        }

        // Build the authorization header
        $authorizationHeader .= 'Bearer ' . $this->apiToken;

        // Initialize Guzzle client with base URI and headers
        $this->client = new Client([
            'base_uri' => $this->apiUrl,
            'headers' => [
                'Authorization' => $authorizationHeader,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Sets or updates the API token (Bearer token).
     *
     * @param string $apiToken
     */
    public function setApiToken(string $apiToken): void
    {
        $this->apiToken = $apiToken;
        $this->initializeClient(); // Re-initialize the client with the new token
    }

    /**
     * Sets or updates the Basic Auth credentials.
     *
     * @param string|null $basicAuth
     */
    public function setBasicAuth(?string $basicAuth): void
    {
        $this->basicAuth = $basicAuth;
        $this->initializeClient(); // Re-initialize the client with the new auth
    }


    /**
     * Fetch a list of conversations for a given user.
     *
     * @param string $userId
     * @return array
     * @throws GuzzleException
     */
    public function getConversations(string $userId): array
    {
        try {
            // Make a GET request to fetch conversations
            $response = $this->client->get("conversations", [
                'query' => [
                    'user_id' => $userId,
                ]
            ]);

            // Decode JSON response and map to Conversation objects
            $data = json_decode($response->getBody(), true);
            return array_map(fn($item) => new Conversation($item), $data["data"]);

        } catch (GuzzleException $e) {
            // Log error or handle exception
            throw $e;
        }
    }

    /**
     * Find or create a conversation.
     *
     * @param ConversationOption $conversationOption
     * @return Conversation
     * @throws GuzzleException
     */
    public function findOrCreateConversation(ConversationOption $conversationOption): Conversation
    {
        try {
            // Make a POST request to create a conversation
            $response = $this->client->post("conversations", [
                'json' => $conversationOption->toArray(),
                'headers' => [
                    'x-llm-key' => $this->openAIToken,
                ],
            ]);

            // Decode response and return a Conversation object
            $data = json_decode($response->getBody(), true);
            return new Conversation($data["data"]);

        } catch (GuzzleException $e) {
            // Handle or log the exception
            throw $e;
        }
    }

    /**
     * Retrieve a specific conversation by its ID.
     *
     * @param int $conversationId
     * @return Conversation
     * @throws GuzzleException
     */
    public function getConversation(int $conversationId): Conversation
    {
        try {
            // Make a GET request to fetch a conversation by ID
            $response = $this->client->get("conversations/{$conversationId}", [
                'headers' => [
                    'x-llm-key' => $this->openAIToken,
                ]
            ]);

            // Decode response and return a Conversation object
            $data = json_decode($response->getBody(), true);
            return new Conversation($data["data"]);

        } catch (GuzzleException $e) {
            // Handle or log the exception
            throw $e;
        }
    }

    /**
     * Cancel an active task run in a conversation.
     *
     * @param int $conversationId
     * @return bool
     */
    public function cancelRun(int $conversationId): bool
    {
        try {
            // Make a POST request to cancel the active run
            $this->client->post("conversations/{$conversationId}/cancel-run");
            return true;
        } catch (GuzzleException $e) {
            // Log the error and return false
            return false;
        }
    }

    /**
     * Update a conversation.
     *
     * @param int $conversationId
     * @param ConversationUpdateOption $updateOption
     * @return Conversation
     * @throws GuzzleException
     */
    public function updateConversation(int $conversationId, ConversationUpdateOption $updateOption): Conversation
    {
        try {
            // Make a PATCH request to update a conversation
            $response = $this->client->patch("conversations/{$conversationId}", [
                'json' => $updateOption->toArray(),
            ]);

            // Decode response and return a Conversation object
            $data = json_decode($response->getBody(), true);
            return new Conversation($data["data"]);

        } catch (GuzzleException $e) {
            // Handle or log the exception
            throw $e;
        }
    }

    /**
     * Deactivate a conversation.
     *
     * @param int $conversationId
     * @return array
     * @throws GuzzleException
     */
    public function deactivateConversation(int $conversationId): array
    {
        try {
            // Make a DELETE request to deactivate a conversation
            $response = $this->client->delete("conversations/{$conversationId}");
            return json_decode($response->getBody(), true);
        } catch (GuzzleException $e) {
            // Handle or log the exception
            throw $e;
        }
    }

    /**
     * Create a message in a conversation.
     *
     * @param int $conversationId
     * @param MessageOption $messageOption
     * @return array
     * @throws GuzzleException
     */
    public function createMessage(int $conversationId, MessageOption $messageOption): array
    {
        try {
            // Make a POST request to create a message
            $response = $this->client->post("conversations/{$conversationId}/messages", [
                'json' => $messageOption->toArray(),
                'headers' => [
                    'x-llm-key' => $this->openAIToken,
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (GuzzleException $e) {
            // Handle or log the exception
            throw $e;
        }
    }

    /**
     * Initiate a task run for a specific task.
     *
     * @param string $taskKey
     * @param TaskRunOption $taskRunOption
     * @return array
     * @throws GuzzleException
     */
    public function initiateTaskRun(string $taskKey, TaskRunOption $taskRunOption): array
    {
        try {
            // Make a POST request to initiate the task run
            $response = $this->client->post("tasks/{$taskKey}/runs", [
                'json' => $taskRunOption->toArray(),
                'headers' => [
                    'x-llm-key' => $this->openAIToken,
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (GuzzleException $e) {
            throw $e;
        }
    }

    /**
     * Get the task run output.
     *
     * @param string $taskKey
     * @param int $runId
     * @return TaskOutput
     * @throws GuzzleException
     */
    public function getTaskRun(string $taskKey, int $runId): TaskOutput
    {
        try {
            // Make a GET request to fetch the task run output
            $response = $this->client->get("tasks/{$taskKey}/runs/{$runId}");

            // Decode response and return TaskOutput object
            $data = json_decode($response->getBody(), true);
            return new TaskOutput($data["data"]);
        } catch (GuzzleException $e) {
            throw $e;
        }
    }

    /**
     * Poll the task run until it is finished and return the output.
     *
     * @param string $taskKey
     * @param int $runId
     * @param int $intervalInSeconds
     * @param int $maxRetries
     * @return TaskOutput
     * @throws GuzzleException
     */
    public function pollTaskRunUntilComplete(string $taskKey, int $runId, int $intervalInSeconds = 1, int $maxRetries = 60): TaskOutput
    {
        $retryCount = 0;

        while ($retryCount < $maxRetries) {
            try {
                $taskOutput = $this->getTaskRun($taskKey, $runId);

                if (!$taskOutput->is_running) {
                    // Task has completed, return the output
                    return $taskOutput;
                }

                // Task is still running, wait before polling again
                sleep($intervalInSeconds);
                $retryCount++;

            } catch (GuzzleException $e) {
                throw $e;
            }
        }

        throw new \Exception("Polling exceeded the maximum number of retries.");
    }

    /**
     * Initiate a task run and poll until it completes.
     *
     * @param string $taskKey
     * @param TaskRunOption $taskRunOption
     * @return TaskOutput
     * @throws GuzzleException
     */
    public function initiateTaskRunAndPoll(string $taskKey, TaskRunOption $taskRunOption): TaskOutput
    {
        // Initiate the task run
        $taskRunResponse = $this->initiateTaskRun($taskKey, $taskRunOption);
        $runId = $taskRunResponse['run_id'];

        // Poll the task run until it's complete
        return $this->pollTaskRunUntilComplete($taskKey, $runId);
    }
}
