# GifExceptionBundle

[![Latest Stable Version](https://poser.pugx.org/jolicode/gif-exception-bundle/v/stable)](https://packagist.org/packages/jolicode/gif-exception-bundle)
[![Total Downloads](https://poser.pugx.org/jolicode/gif-exception-bundle/downloads)](https://packagist.org/packages/jolicode/gif-exception-bundle)
[![Build Status](https://travis-ci.org/jolicode/GifExceptionBundle.svg?branch=master)](https://travis-ci.org/jolicode/GifExceptionBundle)

Very important bundle to make Symfony Exception looks like /r/gifs.

![Demo](Resources/doc/images/demo.gif)

*Be aware that we can not be held responsible for any loss of productivity during development.*

## Installation

- Use [Composer](http://getcomposer.org/) to install `GifExceptionBundle` in your project:

```shell
composer require "jolicode/gif-exception-bundle"
```

- Enable the bundle in **dev** environment:

```php
    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        $bundles = [
            // ...
        ];

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            // ...
            $bundles[] = new \Joli\GifExceptionBundle\GifExceptionBundle();
        }

        return $bundles;
    }
```

- Then install the assets to make gifs accessible to public:

```shell
app/console assets:install
```

Now enjoy your exceptions \o/

## Add some more gifs!

We need you to improve the included GIFs! Do not hesitate to open PRs to add 
more gifs in [Resources/public/images/](Resources/public/images/), it will be very welcomed! :wink:

## Further documentation

You can see the current and past versions using one of the following:

* the `git tag` command
* the [releases page on Github](https://github.com/jolicode/GifExceptionBundle/releases)
* the file listing the [changes between versions](CHANGELOG.md)

And some meta documentation:

* [versioning and branching models](VERSIONING.md)
* [contribution instructions](CONTRIBUTING.md)

## Credits

* [All contributors](https://github.com/jolicode/GifExceptionBundle/graphs/contributors);
* All GIFs included belong to their respective authors.

## License

GifExceptionBundle is licensed under the MIT License - see the [LICENSE](LICENSE) file
for details.
