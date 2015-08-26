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

### Transferring between databases

- Creates a local table
- Exports the table to a file
- Imports the file into the table

```php
$redshiftTable
    ->createTable($localTable)
    ->export($localFile)
    ->import($localTable);
```

### Moving files around
```php
$localFile
    ->compress(CompressionType::GZIP)
    ->transfer($s3File)
```

### Transferring files from ftp modifying them and upload to another filesystem

- Grab a bunch of files from a filesystem based on their file metadata (name regex and timestamp created from 2015 onwards)
- Copy each the file locally
  - Convert the encoding
  - Replace NULL with \\N in each file
  - Move the file to another file system

```php
$ftpSource = new FileSource(
    $ftpFileSystem,
    '/path/to/files/',
    $filterFactory->createFilters([
        'name ~' => '/^name_of_file_\d+.csv$/i',
        'timestamp >' => '2015-01-01'
    ])
);

$ftpSource
    ->getFiles(true) // FileNodeCollectionInterface
    ->map(function ($file) use ($localDir, $remoteDir) {
        $file
            ->copyTo(new File($localDir->getFilesystem(), $localDir->getDirectory() . $file->getFilename()) // FileNodeInterface
            ->toEncoding('utf8') // FileNodeInterface
            ->replaceText('NULL','\\N') // FileNodeInterface
            ->moveTo(new File($remoteDir->getFilesystem(), $remoteDir->getDirectory() . $file->getFilename()); // FileNodeInterface
    }); //FileNodeCollectionInterface (of each file on remoteFileSystem)
```

### Custom flows based on other flows
```php
$localTable
    ->copyTo($redshiftTable);
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ make test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email harry.bragg@graze.com instead of using the issue tracker.

## Credits

- [Harry Bragg](https://github.com/h-bragg)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
