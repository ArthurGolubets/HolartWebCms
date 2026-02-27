<?php

namespace HolartWeb\HolartCMS\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use HolartWeb\HolartCMS\Services\LicenseService;

class UpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'holartcms:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновить HolartCMS: конфигурацию и пересобрать фронтенд';

    /**
     * License service instance.
     */
    protected LicenseService $licenseService;

    /**
     * Create a new command instance.
     */
    public function __construct(LicenseService $licenseService)
    {
        parent::__construct();
        $this->licenseService = $licenseService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔄 Обновление HolartCMS...');
        $this->newLine();

        // Check license
        if (!$this->checkLicense()) {
            $this->error('❌ Обновление отменено: недействительный лицензионный ключ');
            return self::FAILURE;
        }

        $this->newLine();

        // 1. Publish config
        $this->info('📦 Публикация конфигурации...');
        $this->call('vendor:publish', [
            '--tag' => 'holart-cms-config',
            '--force' => true,
        ]);
        $this->newLine();

        // 2. Build frontend
        $this->info('🎨 Сборка фронтенда...');
        $this->buildFrontend();
        $this->newLine();

        // 3. Publish assets
        $this->info('📦 Публикация assets...');
        $this->call('vendor:publish', [
            '--tag' => 'holart-cms-assets',
            '--force' => true,
        ]);
        $this->newLine();

        // 4. Clear cache
        $this->info('🧹 Очистка кэша...');
        $this->call('config:clear');
        $this->call('route:clear');
        $this->call('view:clear');
        $this->newLine();

        $this->info('✅ HolartCMS успешно обновлен!');

        return self::SUCCESS;
    }

    /**
     * Build the frontend assets.
     */
    protected function buildFrontend(): void
    {
        $packagePath = dirname(__DIR__, 2);

        // Check if node_modules exists
        if (!File::exists($packagePath . '/node_modules')) {
            $this->warn('📥 Установка npm зависимостей...');
            exec("cd {$packagePath} && npm install 2>&1", $output, $returnCode);

            if ($returnCode !== 0) {
                $this->error('❌ Ошибка при установке npm зависимостей');
                $this->line(implode("\n", $output));
                return;
            }

            $this->info('✅ npm зависимости установлены');
        }

        // Build assets
        $this->info('🔨 Сборка assets...');
        exec("cd {$packagePath} && npm run build 2>&1", $output, $returnCode);

        if ($returnCode !== 0) {
            $this->error('❌ Ошибка при сборке assets');
            $this->line(implode("\n", $output));
            return;
        }

        $this->info('✅ Assets собраны успешно');
    }

    /**
     * Check license key.
     */
    protected function checkLicense(): bool
    {
        $this->info('🔑 Проверка лицензии...');

        // Check saved license
        if (!$this->licenseService->hasValidLicense()) {
            $this->error('❌ Лицензия недействительна или отсутствует');
            $this->line('Пожалуйста, переустановите HolartCMS с действительной лицензией');
            return false;
        }

        $this->info('✅ Лицензия действительна');
        return true;
    }
}
