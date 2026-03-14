# Incident Report: WordPress Memory Limit Failure

## Summary
A critical issue occurred where WordPress consistently reported a memory limit of 128M despite server-level configuration being set to 512M. This caused plugin failures, admin crashes, and instability across the site.

## Date of Incident
03-MAR-2026

## Impact
- WordPress admin pages failed to load
- Divi Builder crashed repeatedly
- PeepSo and PMPro operations failed
- Site experienced 60–80 second timeouts
- Debug logs filled with memory-related errors

## Affected Components
- WordPress core
- PHP handler (ALT-PHP vs EA-PHP)
- LiteSpeed
- CloudLinux LVE
- PeepSo, PMPro, Divi

## Resolution Summary
A custom MU-plugin was created to enforce the correct memory limit at multiple stages of the WordPress bootstrap process, overriding conflicting PHP handlers.