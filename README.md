# WordPress Memory Limit Incident  
A technical case study documenting how WordPress repeatedly loaded with a 128M memory limit despite server settings being 512M — and how a custom MU‑plugin permanently resolved the issue.

---

## 📌 Overview

This repository documents a real-world debugging incident involving:

- CloudLinux + LiteSpeed  
- ALT-PHP vs EA-PHP  
- WordPress memory limit inconsistencies  
- Plugins failing due to insufficient memory  
- A custom MU-plugin that forces the correct memory limit at every stage of the WP bootstrap process  

The goal of this repo is to provide a **reproducible fix**, a **clear root-cause analysis**, and a **reference for anyone facing similar issues**.

---

## 🚨 The Problem

Despite the server being configured for **512M**, WordPress consistently reported:

- `WP_MEMORY_LIMIT = 128M`
- `WP_MAX_MEMORY_LIMIT = 128M`
- Divi Builder crashing
- PeepSo and PMPro failing to load
- 60–80 second timeouts
- Site Health showing incorrect memory values

Even after:

- Editing `php.ini`
- Editing `.htaccess`
- Editing `wp-config.php`
- Changing memory settings in cPanel
- Changing memory settings in WHM

**WordPress still loaded with 128M.**

---

## 🧠 Root Cause Summary

The server was running **ALT-PHP**, which:

- Ignores local `php.ini`
- Ignores `.htaccess` memory settings
- Overrides WordPress memory settings
- Loads with a fixed memory limit unless overridden internally

Additionally:

- Several plugins attempted to set their own memory limits  
- LiteSpeed read memory from CloudLinux LVE  
- WordPress bootstrap stages overwrote each other’s values  

The only reliable solution was to enforce the memory limit **inside WordPress**, at the MU‑plugin level.

Full details:  
👉 See `/docs/root-cause-analysis.md`

---


##  📁 Repository Structure

```
wordpress-memory-limit-incident/
│
├── mu-plugin/
│   └── force-memory-limit.php
│
├── tests/
│   └── memory-test.php
│
├── docs/
│   ├── incident-report.md
│   ├── root-cause-analysis.md
│   ├── troubleshooting-steps.md
│   ├── php-memory-incident-github-report.pdf
│   ├── environment-details.md
│   └── verification.md
│
└── README.md
```

## 🛠️ The Solution: MU‑Plugin

A custom MU-plugin was created to:

- Set memory limit immediately on load  
- Reapply it at every major WordPress bootstrap stage  
- Use priority `99999` to override all other plugins  

📄 File: `mu-plugin/force-memory-limit.php`

```php
<?php
/**
 * Plugin Name: Force Memory Limit
 * Description: Forces PHP memory limit to 512M at multiple stages of WordPress loading.
 * Author: Bithun Chatterjee
 * Version: 1.0.0
 */

/**
 * Set memory limit immediately when the file loads.
 * MU-plugins load before all other plugins, so this ensures
 * the memory limit is applied as early as possible.
 */
ini_set('memory_limit', '512M');

/**
 * Callback function to re-apply the memory limit.
 * Some plugins or PHP handlers override memory_limit later,
 * so we hook this function into multiple stages of WP bootstrap.
 */
$force_memory = function() {
    ini_set('memory_limit', '512M');
};

/**
 * I use priority 99999 to ensure our memory override runs LAST.
 * This guarantees that even if other plugins try to lower the memory limit,
 * our value (512M) will overwrite theirs.
 */
add_action('muplugins_loaded', $force_memory, 99999);
add_action('plugins_loaded', $force_memory, 99999);
add_action('after_setup_theme', $force_memory, 99999);
add_action('init', $force_memory, 99999);
add_action('wp_loaded', $force_memory, 99999);
```

## 🔍 Verification
A test script was created to confirm the memory limit before and after WordPress loads.

📄 File: `tests/memory-test.php`

**Expected output:**

```
Before wp-load: 512M
After wp-load: 512M
```

Full verification details:
👉 See `/docs/verification.md`

## 🖼️ Screenshots (Before & After)



###  Before Fix

- phpinfo (via browser)

<img width="922" height="183" src="screenshots\php-info-before.png" />

- WordPress Site-health

<img width="744" height="434" src="screenshots\site-health-before.png" />



###  After Fix

- phpinfo (via browser)

<img width="1035" height="190" src="screenshots\php-info-after.png" />

- WordPress Site-health

<img width="796" height="432" src="screenshots\site-health-after.png" />



##  Manual Attempts

- php-override-manually

<img width="694" height="380" src="screenshots\php-override-manually.png" />

- Cloud-linux-server override

<img width="1072" height="655" src="screenshots\cloud-linux-server-override.png" />

- cPanel-php-selector-memory-override

<img width="1090" height="1117" src="screenshots\cPanel-php-selector-memory-override.png" />

- cPanel-memory-override

<img width="1340" height="1019" src="screenshots\cPanel-memory-override.png" />

- whm-memory-override-for-altphp81

<img width="1231" height="838" src="screenshots\whm-memory-override-for-altphp81.png" />

- whm-memory-override-for-ea-php81

<img width="1222" height="840" src="screenshots\whm-memory-override-for-eaphp81.png" />








##  🛠️ Full technical report
👉 See `/docs/php-memory-incident-github-report.pdf`


<br>
<br>

##  🧑‍💻 Author
Bithun Chatterjee (Golden)  
Senior Technical Consultant, Volue
Trondheim, Norway