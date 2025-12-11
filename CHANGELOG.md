# Changelog

All notable changes to `Filament Forum` will be documented in this file.

## v2.0.4 - 2025-12-11

### What's Changed

* Add columnSpanFull() by @andreia in https://github.com/TappNetwork/Filament-Forum/pull/10

**Full Changelog**: https://github.com/TappNetwork/Filament-Forum/compare/v2.0.3...v2.0.4

## v2.0.3 - 2025-11-13

**Full Changelog**: https://github.com/TappNetwork/Filament-Forum/compare/v2.0.2...v2.0.3

## v2.0.1 - 2025-11-13

**Full Changelog**: https://github.com/TappNetwork/Filament-Forum/compare/v2.0.0...v2.0.1

bugfix

## v2.0.0 - 2025-11-13

### What's Changed

* Add forum access control and consolidate user traits by @scottgrayson in https://github.com/TappNetwork/Filament-Forum/pull/8

### New Contributors

* @scottgrayson made their first contribution in https://github.com/TappNetwork/Filament-Forum/pull/8

**Full Changelog**: https://github.com/TappNetwork/Filament-Forum/compare/v1.1.0...v2.0.0

### Breaking Changes

- The three separate traits (HasFavoriteForumPost, HasMentionables, HasForumUserSearch) have been consolidated into a single `ForumUser` trait. Users should update their User model to use `ForumUser` instead of the three separate traits.

## v1.1.0 - 2025-10-08

### What's Changed

Remove dependency https://github.com/kirschbaum-development/commentions in favor of TipTap editor for rich text and image attachments.

* Update comments by @andreia in https://github.com/TappNetwork/Filament-Forum/pull/5

**Full Changelog**: https://github.com/TappNetwork/Filament-Forum/compare/v1.0.0...v1.1.0

## v1.0.0 - 2025-10-02

### What's Changed

* Tests and Workflows by @swilla in https://github.com/TappNetwork/Filament-Forum/pull/1
* Add edited to post by @andreia in https://github.com/TappNetwork/Filament-Forum/pull/2
* Update migrations by @andreia in https://github.com/TappNetwork/Filament-Forum/pull/3
* Add custom title attribute to user by @andreia in https://github.com/TappNetwork/Filament-Forum/pull/4

### New Contributors

* @swilla made their first contribution in https://github.com/TappNetwork/Filament-Forum/pull/1
* @andreia made their first contribution in https://github.com/TappNetwork/Filament-Forum/pull/2

**Full Changelog**: https://github.com/TappNetwork/Filament-Forum/commits/v1.0.0
