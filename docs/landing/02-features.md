---
view: components.packages.features
variant: compact
badge: Key Features
title: Everything a deploy step needs
subtitle: One file per task and one command in your deploy script.
items:
  - icon: check-circle
    title: Run Once or Always
    text: Mark an executor `ExecutorType::ONCE` to run it a single time, or `ALWAYS` to run it on every deploy.
  - icon: terminal
    title: Commands and Jobs
    text: Call Artisan with `$this->command()` and dispatch queued jobs with `$this->job()` from `up()`.
  - icon: database
    title: Batches and Rollback
    text: Every run is recorded in a batch, and `execute:rollback` calls `down()` on the latest batches.
  - icon: tag
    title: Tags
    text: Tag executors and run a subset with `php artisan execute --tags=cache,search`.
  - icon: globe
    title: Server Targeting
    text: List server IPs in `$servers` to keep an executor to the machines it belongs on.
  - icon: eye
    title: Status at a Glance
    text: '`execute:status` shows every executor with its type, tags, servers, batch and whether it ran.'
---
