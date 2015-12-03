param([string]$path)

phpcs --config-set installed_paths %cd%\vendor\wp-coding-standards\wpcs
phpcs --standard=WordPress --ignore=vendor $path
