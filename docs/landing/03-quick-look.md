---
view: components.packages.quick-look
title: A quick look
subtitle: An executor that rebuilds the caches on every deploy, on the web servers only.
file: executors/2026_01_15_120000_rebuild_caches.php
language: php
code: |
  use Pharaonic\Laravel\Executor\Classes\Executor;
  use Pharaonic\Laravel\Executor\Enums\ExecutorType;

  return new class () extends Executor {
      public $type = ExecutorType::ALWAYS;
      public $tags = ['cache'];
      public $servers = ['10.0.0.11', '10.0.0.12'];

      public function up(): void
      {
          $this->command('optimize');
          $this->command('view:cache');
      }

      public function down(): void
      {
          $this->command('optimize:clear');
      }
  };
---
