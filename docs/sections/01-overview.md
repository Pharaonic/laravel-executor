:::badges
- Laravel Package {color=blue}
- {release.label} {color=green}
- {package.license} License {color=purple}
:::

# Laravel Executor

Laravel Executor turns the steps you run after a deploy into versioned PHP files called executors. Each executor has an `up()` method that does the work and a `down()` method that reverses it. `php artisan execute` runs every executor the server still needs and records the run in a database table, in batches you can roll back, filter by tag, and limit to specific servers.

:::features
### Run Once or Always {icon="check-circle"}
An executor runs a single time per database, or again on every deploy.

### Commands and Jobs {icon="terminal"}
Call Artisan commands and dispatch queued jobs from inside an executor.

### Batches and Rollback {icon="database"}
Every run is recorded in a batch, and the latest batches can be rolled back.

### Tags {icon="tag"}
Group executors with tags and run only the groups you need.

### Server Targeting {icon="globe"}
List the server IP addresses an executor belongs to, and other servers skip it.

### Status {icon="eye"}
See every executor with its type, tags, servers, batch and whether it ran.
:::

:::info Quick Tip
Add `php artisan execute` to your deploy script right after `php artisan migrate`, and run it on every server.
:::
