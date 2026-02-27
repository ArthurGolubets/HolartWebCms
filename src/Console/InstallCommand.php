<?php

namespace HolartWeb\HolartCMS\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use HolartWeb\HolartCMS\Models\TAdministrator;
use HolartWeb\HolartCMS\Enums\AdminRole;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'holartcms:install {--force : Force installation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install HolartCMS admin panel';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Установка HolartCMS...');
        $this->newLine();

        // Publish configuration
        $this->info('📦 Публикация конфигурации...');
        $this->call('vendor:publish', [
            '--tag' => 'holart-cms-config',
            '--force' => $this->option('force'),
        ]);

        // Run migrations
        $this->info('🗄️  Запуск миграций...');
        $this->call('migrate');

        // Build frontend
        $this->info('🎨 Сборка фронтенда...');
        $this->buildFrontend();

        // Publish assets
        $this->info('📂 Публикация ассетов...');
        $this->call('vendor:publish', [
            '--tag' => 'holart-cms-assets',
            '--force' => $this->option('force'),
        ]);

        $this->newLine();

        // Create super admin
        if ($this->confirm('Создать супер-администратора?', true)) {
            $this->createSuperAdmin();
        }

        $this->newLine();
        $this->info('✅ HolartCMS успешно установлен!');
        $this->newLine();
        $this->line('Админ-панель доступна по адресу: ' . url(config('holart-cms.route_prefix', 'admin')));

        return self::SUCCESS;
    }

    /**
     * Build frontend assets
     */
    protected function buildFrontend(): void
    {
        $packagePath = dirname(__DIR__, 2);

        // Check if node_modules exists
        if (!File::exists($packagePath . '/node_modules')) {
            $this->warn('📥 Установка npm зависимостей...');
            exec("cd {$packagePath} && npm install 2>&1", $output, $returnCode);

            if ($returnCode !== 0) {
                $this->error('Ошибка при установке npm зависимостей');
                $this->line('Попробуйте вручную: cd ' . $packagePath . ' && npm install');
                return;
            }
        }

        // Build assets
        $this->line('🔨 Сборка ассетов...');
        exec("cd {$packagePath} && npm run build 2>&1", $output, $returnCode);

        if ($returnCode === 0) {
            $this->info('✅ Фронтенд успешно собран!');
        } else {
            $this->error('Ошибка при сборке фронтенда');
            $this->line('Попробуйте вручную: cd ' . $packagePath . ' && npm run build');
        }
    }

    /**
     * Create super admin user
     */
    protected function createSuperAdmin(): void
    {
        $this->info('👤 Создание супер-администратора...');

        $name = $this->ask('Имя', 'Супер Администратор');
        $email = $this->ask('Email', 'admin@holartcms.local');
        $password = $this->secret('Пароль (минимум 8 символов)');
        $passwordConfirmation = $this->secret('Подтвердите пароль');

        if ($password !== $passwordConfirmation) {
            $this->error('❌ Пароли не совпадают!');
            return;
        }

        if (strlen($password) < 8) {
            $this->error('❌ Пароль должен содержать минимум 8 символов!');
            return;
        }

        // Check if email exists
        if (TAdministrator::where('email', $email)->exists()) {
            $this->error('❌ Пользователь с таким email уже существует!');
            return;
        }

        try {
            TAdministrator::create([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt($password),
                'role' => AdminRole::SUPER_ADMIN->value,
                'is_active' => true,
            ]);

            $this->info('✅ Супер-администратор успешно создан!');
            $this->table(
                ['Имя', 'Email', 'Роль'],
                [[$name, $email, 'Супер Администратор']]
            );
        } catch (\Exception $e) {
            $this->error('❌ Ошибка создания администратора: ' . $e->getMessage());
        }
    }
}
