# Root Cause Analysis

## Primary Root Cause
The server was running **ALT-PHP**, which ignores local *php.ini* and .htaccess ´memory_limit´ settings. WordPress was therefore always initialized with **128M**, regardless of server configuration.

## Contributing Factors
- LiteSpeed was reading memory limits from CloudLinux LVE, not php.ini
- WordPress Site Health displayed incorrect values
- Plugins attempted to override memory_limit during bootstrap
- No OPcache, causing repeated re-parsing of PHP files
- Divi builder, PeepSo and PMPro triggered heavy operations that required more memory

## Why Normal Fixes Failed
- Editing php.ini had no effect under ALT-PHP
- Editing .htaccess had no effect
- Editing wp-config.php had no effect
- Hosting control panel settings were overridden by the PHP handler

## Final Understanding
The memory limit needed to be enforced **inside WordPress itself**, at the MU-plugin level, using late-priority hooks to override all other plugin attempts.