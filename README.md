# MediaBundle

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Braunstetter/media-bundle/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/Braunstetter/media-bundle/?branch=main)
[![Code Coverage](https://scrutinizer-ci.com/g/Braunstetter/media-bundle/badges/coverage.png?b=main)](https://scrutinizer-ci.com/g/Braunstetter/media-bundle/?branch=main)
[![Build Status](https://app.travis-ci.com/Braunstetter/media-bundle.svg?branch=main)](https://app.travis-ci.com/Braunstetter/media-bundle)
[![Total Downloads](http://poser.pugx.org/braunstetter/media-bundle/downloads)](https://packagist.org/packages/braunstetter/media-bundle)
[![License](http://poser.pugx.org/braunstetter/media-bundle/license)](https://packagist.org/packages/braunstetter/media-bundle)

This Bundle aims to make working with media entities easy.

It provides a general API for everything related to media objects without restricting your flexibility.

* [The BaseFile entity](#the-basefile-entity)
  * [Important methods](#important-methods)
    * [getFullPath](#getfullpath)
    * [getType](#gettype)
* [Uploader](#uploader)
  * [FilesystemUploader](#filesystem-uploader)
* [Contributing](#contributing)
  * [Testing](#testing)
  * [Roadmap](#roadmap)

# The BaseFile entity

A doctrine MappedSuperclass your files should always extend.

It provides everything basic you need to get the ball rolling.

Create file `App\Entity\Image`:

```php
<?php

namespace App\Entity;

use Braunstetter\MediaBundle\Entity\BaseFile;

class Image extends BaseFile
{

}
```

And migrate your database:

```shell
symfony console make:migration && symfony console doctrine:migrations:migrate'
```

It doesn't look like much - but at this point, you're using an interface (`FileInterface`) your uploaders and forms can work with.  
Furthermore, you just got a few basic methods and properties you can further manipulate and extend.

## Important methods

These are methods you're definitely should be familiar with.  
Outer properties and methods of the BaseFile entity are documented inside the class itself.
### getFullPath

This method puts together a `folder` and `filename`.  
These properties both have to be set. Otherwise, this method returns null.

### getType

The type of your file.  
By default, it's the name of the file entity class in lowercase.  
You should name your media entities with a media type in mind.
E.g. `Image` or `Document`.

# Uploader

An uploader is just a class you can use:

```php
$uploader->setFolder('/image/redactor')
$uploader->upload($imageEntity)
```
You are working directly with your entity (not with a file or its path).  
As long as your form has filled the `file` property with a file. The image will be uploaded. That's super easy and fun.

By default, the uploader will save the file to `public/image/redactor/my-slugged-filename_2315g323.jpeg`.
Pay attention to the filename. It is getting slugged by default - and it is getting suffixed with an uniq id. You can disable the `uniqFileName` by passing `false` as a second argument. 

```php 
$uploader->upload($imageEntity, false)
```


Currently, this bundle ships with these uploaders:

##  Filesystem Uploader
Saves files to the local filesystem. Nothing special here at the moment.

# Contributing

If you think this bundle could still be improved and expanded, then we welcome your PR.

## Testing

To make sure everything works fine - you have to run the test suite.

You need to make sure [Panther](https://github.com/symfony/panther#installing-chromedriver-and-geckodriver) is working properly on your machine.  
Then your tests should work fine performing a simple: 

```shell
./vendor/phpunit/phpunit/phpunit
```

## Roadmap

There are still some features that are definitely to come.

1. [ ] S3 Uploader
2. [ ] Draggable UI (make media objects draggable)

You are welcome to help to implement those features.
If you have any suggestions on what's also missing - don't hesitate to open an issue.

> Another good idea is to write a `MediaLibraryBundle` on top of this Bundle - using the symfony-ux ecosystem.