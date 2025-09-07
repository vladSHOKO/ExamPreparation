<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SetupOpenAI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'openai:setup {--disable-ssl : Disable SSL verification for development}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup OpenAI configuration and add required environment variables';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Setting up OpenAI configuration...');

        $envPath = base_path('.env');
        
        if (!File::exists($envPath)) {
            $this->error('.env file not found!');
            return 1;
        }

        $envContent = File::get($envPath);
        
        // Добавляем переменные OpenAI если их нет
        $openaiVars = [
            'OPENAI_API_KEY=your_openai_api_key_here',
            'OPENAI_MODEL=gpt-5-mini',
            'OPENAI_BASE_URL=https://api.openai.com/v1',
            'OPENAI_TIMEOUT=300',
            'OPENAI_CONNECT_TIMEOUT=60',
            'OPENAI_MAX_TOKENS=800',
            'OPENAI_MAX_COMPLETION_TOKENS=2000',
            'OPENAI_TEMPERATURE=1.0',
            'OPENAI_TOP_P=0.5',
            'OPENAI_FREQUENCY_PENALTY=0.0',
            'OPENAI_PRESENCE_PENALTY=0.0',
        ];

        if ($this->option('disable-ssl')) {
            $openaiVars[] = 'OPENAI_VERIFY_SSL=false';
            $this->warn('SSL verification will be disabled for OpenAI requests');
        } else {
            $openaiVars[] = 'OPENAI_VERIFY_SSL=true';
        }

        $addedVars = [];
        foreach ($openaiVars as $var) {
            $varName = explode('=', $var)[0];
            if (!str_contains($envContent, $varName)) {
                $envContent .= "\n" . $var;
                $addedVars[] = $varName;
            }
        }

        if (!empty($addedVars)) {
            File::put($envPath, $envContent);
            $this->info('Added environment variables: ' . implode(', ', $addedVars));
        } else {
            $this->info('All OpenAI environment variables already exist');
        }

        $this->info('OpenAI setup completed!');
        $this->line('');
        $this->warn('Don\'t forget to:');
        $this->line('1. Get your OpenAI API key from https://platform.openai.com/api-keys');
        $this->line('2. Replace "your_openai_api_key_here" with your actual API key');
        $this->line('3. Run "php artisan config:clear" to reload configuration');

        return 0;
    }
}
