# MediaBundle

[![Total Downloads](http://poser.pugx.org/braunstetter/media-bundle/downloads)](https://packagist.org/packages/braunstetter/media-bundle)
[![License](http://poser.pugx.org/braunstetter/media-bundle/license)](https://packagist.org/packages/braunstetter/media-bundle)

This Bundle aims to make working with media entities easy.

It provides a general API for everything related to media objects without restricting your flexibility.

* [The BaseFile entity](#the-basefile-entity)
    * [Important methods](#important-methods)
    * [Connecting media entities](#connecting-media-entities)
* [FormTypes](#formtypes)
    * [Full example](#full-example)
    * [Available FormTypes](#available-formtypes)
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
symfony console make:migration && symfony console doctrine:migrations:migrate
```

It doesn't look like much - but at this point, you're using an interface (`FileInterface`) your uploaders and forms can
work with.  
Furthermore, you just got a few basic methods and properties you can further manipulate and extend.

## Important methods

These are methods you definitely should be familiar with.  
Outer properties and methods of the BaseFile entity are documented inside the class itself.

### getFullPath

This method puts together a `folder` and `filename`.  
These properties both have to be set. Otherwise, this method returns null.

### getType

The type of your file.  
By default, it's the name of the file entity class in lowercase.  
You should name your media entities with a media type in mind.
E.g. `Image` or `Document`.

## Connecting media entities

Once you have defined your media entities you can connect them to other entities.
This would be a complete entity of a `Page` with some `Image`s linked to it.

```php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Media\Image;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity()]
class Page 
{

    #[ORM\ManyToMany(targetEntity: Image::class, cascade: ["ALL"], orphanRemoval: true)]
    private $image;

    public function __construct()
    {
        $this->image = new ArrayCollection();
    }

    public function getImage(): Collection
    {
        return $this->image;
    }
    
    public function addImage(Image $image): self
    {
        if (!$this->image->contains($image)) {
            $this->image[] = $image;
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        $this->image->removeElement($image);

        return $this;
    }
}
```

> For simplicity, it's recommended to use ManyToMany relationships in every case.
> If you need to limit your images, you can do it via a [Validation](https://symfony.com/doc/current/validation.html),
> and/or by using the `max_items` option of the `MediaCollectionType`.

# FormTypes

The nice thing about this bundle is - it ships with FormTypes.

## Full example

This is an example for a collection of editable Image entities:

```injectablephp
$form->add('image', MediaCollectionType::class, [
    'entry_type' => ImageType::class,
    'entry_options' => [
        'required' => false,
        'label' => false,
    ],
    'allow_add' => true,
    'allow_delete' => true
])
```

This will give you a nice UI. This is a screenshot of a simple blanc form:

![blank UI](tests/tests/Functional/Ui/screenshots/empty_form.png)

All screenshots (made automatically by [panther](https://github.com/symfony/panther)) are available inside the
folder: [tests/tests/Functional/Ui/screenshots](/tests/tests/Functional/Ui/screenshots)

> `choose_file` is the translation string of the file-input button.  
> You can translate it by creating a translation file in the media namespace (e.g. media.fr.yaml)
> You can see all available translations
> in [`vendor/braunstetter/media-bundle/src/Resources/translations`](/src/Resources/translations).

## Available FormTypes

If you need more FormTypes, consider opening an issue or contributing by submitting a PR.

### MediaCollectionType

This is the most important type. It inherits from the native `Symfony\Component\Form\Extension\Core\Type\CollectionType`
and can hold a collection of media FormType's.

#### Options

##### max_items

The maximal items allowed for this collection.
Defaults to `9999`.

> At the moment this option is used only by the javascript of this bundle.
> No PHP validation is triggered. If you want to limit your Collection - use a validator.

##### include_css

Determines whether the supplied CSS should be injected.
Defaults to `true`.

If you want to style your collection in a custom way, you can disable the default CSS with this option.

### ImageType

This FormType is dedicated to Images.

#### Options

All options for this FormType go into the `entry_options` options of `MediaCollectionType`:

```php
$form->add('image', MediaCollectionType::class, [
    'entry_type' => ImageType::class,
    'entry_options' => [
        'required' => false,
        'label' => false,
        // ... more options for ImageType
    ],
])
```

##### data_class

If you decide to name your Image entity differently or put it into another namespace, you have to adjust also
the `data_class` option.    
This attribute is by default `App\Entity\Media\Image`.

##### placeholder_image_path

The public path to the placeholder image for this image field.  
It's by default `bundles/media/images/image-placeholder.jpg`.

##### file_options

The array of options for the file field.
A list of available options can be found // available options can be found [here](https://symfony.com/doc/current/reference/forms/types/file.html).  
Defaults to an empty array.

# Uploader

An uploader is just a class you can use:

```php
$uploader->setFolder('/image/redactor')
$uploader->upload($imageEntity)
```

You are working directly with your entity (not with a file or its path).  
As long as your form has filled the `file` property with a file. The image will be uploaded. That's super easy and fun.

By default, the uploader will save the file to `public/image/redactor/my-slugged-filename_2315g323.jpeg`.
Pay attention to the filename. It is getting slugged by default - and it is getting suffixed with an uniq id. You can
disable the `uniqFileName` by passing `false` as a second argument.

```php 
$uploader->upload($imageEntity, false)
```

Currently, this bundle ships with these uploaders:

## Filesystem Uploader

Saves files to the local filesystem. Nothing special here at the moment.

# Contributing

If you think this bundle could still be improved and expanded, then we welcome your PR.

## Testing

To make sure everything works fine - you have to run the test suite.

You need to make sure [Panther](https://github.com/symfony/panther#installing-chromedriver-and-geckodriver) is working
properly on your machine.  
Then your tests should work fine performing a simple:

```shell
yarn --cwd ./src/Resources/assets install --force
yarn --cwd ./tests/app install --force
yarn --cwd ./src/Resources/assets  dev
yarn --cwd ./tests/app dev

./vendor/phpunit/phpunit/phpunit
```

## Roadmap

There are still some features that are definitely to come.

1. [ ] S3 Uploader
2. [ ] Draggable UI (make media objects draggable)
3. [ ] More FormTypes. (Document, PDF, Audio ...)

You are welcome to help to implement those features.
If you have any suggestions on what's also missing - don't hesitate to open an issue.

> Another good idea is to write a `MediaLibraryBundle` on top of this Bundle - using the symfony-ux ecosystem.