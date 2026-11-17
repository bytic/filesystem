# Filesystem Cache

[![Latest Version on Packagist](https://img.shields.io/packagist/v/bytic/cache.svg?style=flat-square)](https://packagist.org/packages/bytic/filesystem)
[![Latest Stable Version](https://poser.pugx.org/bytic/filesystem/v/stable)](https://packagist.org/packages/bytic/filesystem)
[![Latest Unstable Version](https://poser.pugx.org/bytic/filesystem/v/unstable)](https://packagist.org/packages/bytic/filesystem)

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/travis/ByTIC/framework/master.svg?style=flat-square)](https://travis-ci.org/ByTIC/framework)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/f59a8256-9bc0-4443-ae3c-488516d6a3e6/mini.png)](https://insight.sensiolabs.com/projects/f59a8256-9bc0-4443-ae3c-488516d6a3e6)
[![Quality Score](https://img.shields.io/scrutinizer/g/bytic/filesystem.svg?style=flat-square)](https://scrutinizer-ci.com/g/bytic/filesystem)
[![StyleCI](https://styleci.io/repos/118474281/shield?branch=master)](https://styleci.io/repos/118474281)
[![Total Downloads](https://img.shields.io/packagist/dt/bytic/filesystem.svg?style=flat-square)](https://packagist.org/packages/bytic/filesystem)

## Requirements

PHP 8.0.2 or newer and Flysystem 3.35.3 or newer are required. Install
`league/flysystem-aws-s3-v3:^3.0` to use the S3 driver. Current secure AWS SDK
releases require PHP 8.1 or newer.

## Migrating from Flysystem 1 or 2

Existing disk configuration, `FileDisk` helpers and `File` operations remain
available. `File` accepts any Flysystem 3 `FilesystemOperator` and its write,
update, rename and delete helpers return `true` on success; update returns
`false` if the file does not exist. Flysystem 3 reports operation failures by
throwing exceptions, rather than returning `false`. Its native write, copy,
move and delete methods return `void`, and writes overwrite existing files.
`File::getMetadata()` provides type, path, timestamp and size for files and
type and path for directories; other metadata should be retrieved using the
individual Flysystem methods.

Flysystem 1's cached adapter and SFTP adapter are not compatible with
Flysystem 3. The old cache configuration is not applied by this package;
applications that need caching or SFTP must use Flysystem 3-compatible
implementations.
