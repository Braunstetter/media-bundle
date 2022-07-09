# MediaBundle

This Bundle aims to make working with media files easy.

It provides a general api for everything related to media objects without restricting your flexibility working with
symfony and doctrine.

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Braunstetter/media-bundle/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/Braunstetter/media-bundle/?branch=main)
[![Code Coverage](https://scrutinizer-ci.com/g/Braunstetter/media-bundle/badges/coverage.png?b=main)](https://scrutinizer-ci.com/g/Braunstetter/media-bundle/?branch=main)
[![Build Status](https://app.travis-ci.com/Braunstetter/media-bundle.svg?branch=main)](https://app.travis-ci.com/Braunstetter/media-bundle)
[![Total Downloads](http://poser.pugx.org/braunstetter/media-bundle/downloads)](https://packagist.org/packages/braunstetter/media-bundle)
[![License](http://poser.pugx.org/braunstetter/media-bundle/license)](https://packagist.org/packages/braunstetter/media-bundle)

# The BaseFile Entity

This is a doctrine MappedSuperclass your files should always extend.
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

It doesn't look like much - but at this moment you not just got a few basic methods and properties in an entity you can
further manipulate and extend.
You are actually using an interface (`FileInterface`) your file-managers and forms can work with.

## Important properties

### filename

Custom name of a file. It is generated or set by the user depending on your implementation inside your UploadManager

### originalFilename

The original name of the file, before it was getting uploaded.

### mimeType

The mimeType of the file.

### file

This property is only used for uploading.
Therefore, it can be null (nothing to upload and/or change) or it can be an instance of `SplFileInfo`.
This bundle ships with FormTypes which are working with this property to know if a file is ready get processed or not.

### folder

The folder of this file - where the file is located.
It should be the real path from inside the public dir of your application.

## Important methods

### getFullPath

This method puts together `folder` and `filename` if set.
These properties both has to be set. Otherwise, this method returns null.

### getType

The type of your file.
By default, it's the name of the file entity class in lowercase.
You should name your media entities with a media-type in mind.
E.g. `Image` or `Document`.

# Uploader

A file manager is a service for handling your files.

## Usage

Just inject a manager inside your service and use it:

```php
$uploader->setFolder('/image/redactor')
$uploader->upload($imageEntity)
```
Here you are working with your entity. As long as your form has filled the `file` property with a file. The image will be uploaded. That's super easy and fun.

By default, the uploader will save the file to `public/image/redactor/my-slugged-filename_2315g323.jpeg`.
Pay attention to the filename. It is getting slugged by default - and it is getting suffixed with an uniq id. You can disable the `uniqFileName` by passing `false` as a second argument. 

```php 
$uploader->upload($imageEntity, false)
```

Currently, this bundle delivers 1 File-manager:

## FilesystemManager
This file manager saves files to local filesystem. Nothing special here at the moment.