---
view: components.home.faq
badge: FAQ
title: "{package.name}"
highlight: Questions
subtitle: "Quick answers about installing and using {package.name}."
---

## What is {package.name}?

{card.description} It's a free, open-source {technology.name} package by Pharaonic.

## How do I install {package.name}?

Run `composer require {package.composer}` in your project's root directory, then `php artisan migrate`.

## What does {package.name} require?

The latest release requires {package.requiresText}.

## How is an executor different from a migration?

Migrations change the database schema. Executors run anything else a deploy needs, such as Artisan commands, queued jobs or data fixes, and can run once or on every deploy.

## When should I run `php artisan execute`?

Run it in your deploy script after `php artisan migrate`, on every server. Each server runs only the executors it still needs.

## What happens when several servers deploy at the same time?

`php artisan execute` takes a cache lock, so the servers run the executors one after another and a `ONCE` executor runs only once. Use a cache store every server shares, such as Redis or the database.

## Can I keep executors outside the `executors` folder?

Yes. Create them with `php artisan make:executor Name --path=other/folder`, and register the folder with `Executor::getPool()->addPath(base_path('other/folder'))` in a service provider.

## Is {package.name} free to use?

Yes. {package.name} is open source under the {package.license} license, so you can use it in personal and commercial projects.

## Where can I find the {package.name} documentation?

Read the [{package.name} documentation]({package.docsUrl}) for setup, configuration, and usage examples.

## How do I report a bug or contribute to {package.name}?

Open an issue or a pull request on [GitHub]({package.githubUrl}), or ask in the [Pharaonic Discord](https://discord.gg/XQG9RhvEvf).
