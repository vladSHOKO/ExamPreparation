<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\OpenAIChatClient;
use Illuminate\Console\Command;

class TestOpenAIConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'openai:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test OpenAI connection with a simple request';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Testing OpenAI connection...');

        try {
            $client = new OpenAIChatClient();
            
            $this->info('Sending simple test message...');
            
            $messages = [
                ['role' => 'user', 'content' => 'Hello, please respond with just "Hi"']
            ];
            
            $startTime = microtime(true);
            $response = $client->chat($messages);
            $endTime = microtime(true);
            
            $duration = round($endTime - $startTime, 2);
            
            $content = $response['choices'][0]['message']['content'] ?? 'No content';
            
            $this->info("✅ Connection successful!");
            $this->line("Response: {$content}");
            $this->line("Duration: {$duration} seconds");
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("❌ Connection failed: {$e->getMessage()}");
            return 1;
        }
    }
}
