# Data Flow

<img align="right" src="http://media2.giphy.com/media/eYkKx0gbmavMQ/giphy.gif" width="250" />

[![Latest Version on Packagist](https://img.shields.io/packagist/v/graze/data-flow.svg?style=flat-square)](https://packagist.org/packages/graze/data-flow)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/graze/data-flow/master.svg?style=flat-square)](https://travis-ci.org/graze/data-flow)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/graze/data-flow.svg?style=flat-square)](https://scrutinizer-ci.com/g/graze/data-flow/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/graze/data-flow.svg?style=flat-square)](https://scrutinizer-ci.com/g/graze/data-flow)
[![Total Downloads](https://img.shields.io/packagist/dt/graze/data-flow.svg?style=flat-square)](https://packagist.org/packages/graze/data-flow)

To move data from one system to another. Such as sending a table between different database providers, exporting a table and uploading to an ftp site.

## Install

Via Composer

```bash
$ composer require graze/data-flow
```

## Usage

- Move and convert data nodes into other formats
- Soups simple calling `f::each(f::moveFile($targetDir)->gzip()->moveFile($ftpDir))->flow($files)`
  - transfer files from a remote location, compress using gzip and transfer to another location
- [Concrete API](docs/Concrete.md)
- Works with PHP5.6, PHP7 & HHVM

## Commands

### Generic

- `Run` - Iterate through a set of Flows
- `toAll` - Send the same input to each Flow at the same time
- `first` - Take the first node from a collection
- `last` - Take the last node from a collection
- `filter` - Filter out nodes from a collection
- `map` - Apply a function to each node in a collection
- `each` - Apply a FlowInterface to each node in a collection
- `callback` - Apply a callback to the node

### Files

- `makeDirectory` - Make a directory from a file node
- `merge` - Merge a collection of files into a file
- `compress` - Compress a file
- `decompress` - DeCompress a file
- `gzip` - Gzip a file
- `gunzip` - Gunzip a file
- `zip` - Zip a file
- `unzip` - Unzip a file
- `copyFile` - Copy a file to a new location
- `copyFiles` - Cope a collection of files to a new location
- `moveFile` - Move a file to a new location
- `moveFiles` - Move a collection of files to a new location
- `convertEncoding` - Convert the encoding of a file
- `replaceText` - Replace the text in a file
- `tail` - Retrieve the last n lines of a file
- `head` - Retrieve the first n lines of a file

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ make test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email security@graze.com instead of using the issue tracker.

## Credits

- [Harry Bragg](https://github.com/h-bragg)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
