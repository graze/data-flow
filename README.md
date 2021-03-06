# Data Flow

<img align="right" src="http://media2.giphy.com/media/eYkKx0gbmavMQ/giphy.gif" width="250" />

[![Latest Version on Packagist](https://img.shields.io/packagist/v/graze/data-flow.svg?style=flat-square)](https://packagist.org/packages/graze/data-flow)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/graze/data-flow/master.svg?style=flat-square)](https://travis-ci.org/graze/data-flow)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/graze/data-flow.svg?style=flat-square)](https://scrutinizer-ci.com/g/graze/data-flow/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/graze/data-flow.svg?style=flat-square)](https://scrutinizer-ci.com/g/graze/data-flow)
[![Total Downloads](https://img.shields.io/packagist/dt/graze/data-flow.svg?style=flat-square)](https://packagist.org/packages/graze/data-flow)

A `Flow` is a defined series of individual steps each of which modify some data that gets passed to the next step in the flow.

## Details

- Soups simple calling `f::moveFiles($targetDir)->each(f::gzip())->moveFiles($ftpDir)->flow($files)`
  - transfer files from a remote location, compress using gzip and transfer to another location
- [How to flow](docs/Flows.md)
- Works with PHP 5.6, PHP 7 & HHVM
- **N.B.** Uses some command line programs which conform to Ubunutu's syntax.

## Commands

### Generic

- `run` - Iterate through a set of Flows
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

## Installation

Via Composer

```bash
$ composer require graze/data-flow
```

## Testing

`DataFlow` has a `PHPUnit` test suite run through Docker :whale:. To run the tests run the following command:

``` bash
$ make test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Security

If you discover any security related issues, please email security@graze.com instead of using the issue tracker.

## Credits

- [Harry Bragg](https://github.com/h-bragg)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
