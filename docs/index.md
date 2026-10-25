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
  tags: deployment deploy tasks executors artisan commands jobs rollback batches tags servers automation
  description: Run one-time and recurring deployment tasks on your Laravel servers, tracked in batches with rollback, tags and per-server targeting.

seo:
  title: "{package.fullName} - Deployment Tasks for Laravel"
  description: "{package.name} is a Laravel package for running one-time and recurring deployment tasks: Artisan commands, jobs and custom actions, tracked in batches with rollback. {package.downloadsShort}+ downloads, {package.license} licensed."
  keywords: laravel executor, laravel deployment tasks, laravel run once on deploy, laravel artisan commands on deploy, laravel rollback tasks, laravel deploy scripts
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
  description: "{package.name} is a Laravel package for running one-time and recurring deployment tasks, tracked in batches with rollback, tags and per-server targeting."
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
---
