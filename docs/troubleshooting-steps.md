# Troubleshooting Steps

## 1. Verified WordPress Memory Limit
- Site Health showed 128M
- phpinfo() showed 128M
- WP_MAX_MEMORY_LIMIT ignored

## 2. Tested php.ini and .htaccess
- No effect due to ALT-PHP handler

## 3. Switched PHP handlers
- EA-PHP respected php.ini
- ALT-PHP ignored it
- Hosting environment forced ALT-PHP

## 4. Checked CloudLinux LVE
- Memory limit was correct (512M)
- WordPress still loaded with 128M

## 5. Investigated plugin conflicts
- Divi, PeepSo, PMPro all attempted to set memory_limit
- Their values were overwritten by the PHP handler

## 6. Created MU-plugin
- Forced memory_limit at multiple bootstrap stages
- Used priority 99999 to override all other plugins

## 7. Verified fix with test script
- Before wp-load: 512M
- After wp-load: 512M