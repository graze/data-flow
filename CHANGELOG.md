# Change Log

All Notable changes to `data-flow` will be documented in this file

## v1.0 - 2016-02-24

- Initial Release

### Added
- Move and convert data nodes into other formats
- Soups simple calling `f::each(f::moveFile($targetDir)->gzip()->moveFile($ftpDir))->flow($files)`
  - transfer files from a remote location, compress using gzip and transfer to another location
- Concrete API
- Works with PHP5.6, PHP7 & HHVM
