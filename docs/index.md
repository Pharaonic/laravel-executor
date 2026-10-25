---
name: Laravel Executor

action:
  label: View on Packagist
  href: "{package.packagistUrl}"

views: components.packages

breadcrumbs:
  - label: Home
    href: route:home
  - label: Packages
    href: route:packages.index
  - label: "{technology.name} Packages"
    href: "url:/packages/{technology.slug}"
  - label: "{package.name}"

card:
  topic: deployment
  icon: automation
  tags: deployment deploy post-deploy tasks executors one-time operations artisan commands queue jobs data fixes rollback batches tags servers multi-server lock
  description: "Run deployment tasks in Laravel once or on every deploy: Artisan commands, queued jobs and data fixes, tracked in batches with rollback, tags, server targeting and a cross-server lock."

seo:
  title: "{package.fullName} - Run Deployment Tasks Once or on Every Deploy"
  description: "{package.name} runs Laravel deployment tasks once or on every deploy: Artisan commands, queued jobs and data fixes, with batches, rollback, tags and server targeting."
  keywords: laravel executor, laravel deployment tasks, laravel post deploy tasks, run code once after deploy laravel, laravel one-time operations, laravel artisan commands on deploy, laravel data migrations, laravel rollback deploy tasks, laravel multi-server deploy, laravel deploy script
  author: Pharaonic
  images:
    - "{package.cover}"
  openGraph:
    type: website
    siteName: Pharaonic
  twitter:
    card: summary_large_image

schema:
  "@type": SoftwareSourceCode
  name: "{package.name}"
  description: "{package.name} is a Laravel package that runs deployment tasks once or on every deploy: Artisan commands, queued jobs and data fixes, tracked in batches with rollback, tags, per-server targeting and a cross-server lock."
  keywords: laravel, deployment, post-deploy tasks, one-time operations, artisan, queue jobs, rollback, multi-server
  image: "{package.cover}"
  codeRepository: "{package.githubUrl}"
  programmingLanguage: PHP
  runtimePlatform: "{technology.name}"
  version: "{package.version}"
  datePublished: "{package.publishedAt}"
  dateModified: "{package.updatedAt}"
  license: "https://opensource.org/licenses/{package.license}"
  isAccessibleForFree: true
  sameAs:
    - "{package.githubUrl}"
    - "{package.packagistUrl}"
  author:
    "@id": url:/#organization
  publisher:
    "@id": url:/#organization
  interactionStatistic:
    "@type": InteractionCounter
    interactionType: https://schema.org/DownloadAction
    userInteractionCount: "{package.downloads}"

docs:
  seo:
    title: "{package.fullName} Documentation ({versionLabel}) - Laravel Deployment Tasks"
    description:
      - "{package.name} {versionLabel} docs: write executors, run them once or on every deploy, roll back batches, filter by tags and target servers in Laravel."
      - "Write executors, run them once or on every deploy, roll back batches, filter by tags and target servers in Laravel."
    keywords: "{package.name} documentation, {package.composer}, laravel deployment tasks, run code once after deploy, laravel one-time operations, artisan commands on deploy, laravel executor rollback"
---
