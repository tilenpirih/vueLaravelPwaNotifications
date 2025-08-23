<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Minishlink\WebPush\VAPID;

class PushVapidGenerate extends Command
{
    protected $signature = 'push:vapid:generate {--write-env : Write keys into .env file}';
    protected $description = 'Generate VAPID keys for Web Push';

    public function handle(): int
    {
        $keys = VAPID::createVapidKeys();
        $this->info('Public Key:  '.$keys['publicKey']);
        $this->info('Private Key: '.$keys['privateKey']);

        if ($this->option('write-env')) {
            $this->updateEnv('VAPID_PUBLIC_KEY', $keys['publicKey']);
            $this->updateEnv('VAPID_PRIVATE_KEY', $keys['privateKey']);
            $this->updateEnv('VAPID_SUBJECT', config('webpush.vapid.subject', 'mailto:admin@example.com'));
            $this->info('Keys written to .env');
        } else {
            $this->line('To persist, set VAPID_PUBLIC_KEY and VAPID_PRIVATE_KEY in your .env');
        }

        return self::SUCCESS;
    }

    private function updateEnv(string $key, string $value): void
    {
        $envPath = base_path('.env');
        if (!file_exists($envPath)) {
            return;
        }
        $content = file_get_contents($envPath);
        $pattern = "/^{$key}=.*$/m";
        $line = $key.'='.$value;
        if (preg_match($pattern, $content)) {
            $content = preg_replace($pattern, $line, $content);
        } else {
            $content .= "\n{$line}\n";
        }
        file_put_contents($envPath, $content);
    }
}
