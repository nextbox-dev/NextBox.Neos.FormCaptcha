# NextBox.Neos.FormCaptcha

This package adds a captcha element, which can be used within your forms.
This element is rendered with an image and an input field and requires to be filled with the random generated phrase, shown on the image.

If the captcha is not filled correctly, the form will not be submitted and a new image will be generated.

## Installation

The NextBox.Neos.FormCaptcha package is listed on [packagist](https://packagist.org/packages/nextbox/neos-formcaptcha).

Just run the following command within your Distribution:

```shell
composer require nextbox/neos-formcaptcha
```

## Usage with [Neos.Form.Builder](https://github.com/neos/form-builder)

Add a captcha form element to your form.

![NodeType in Neos-Form-Builder](/Documentation/form-builder.png)

## Usage with [Neos.Form](https://github.com/neos/form)

Add the new form element to your form definition renderables:

```yaml
type: 'Neos.Form:Form'
identifier: 'someIdentifier'
renderables:
  -
    type: 'Neos.Form:Page'
    identifier: page-one
    renderables:
      -
        type: 'NextBox.Neos.FormCaptcha:Captcha'
        identifier: 'captcha'
finishers:
  -
    <Your finishers here>
```

## Global settings for Neos.Form and Neos.Form.Builder

You can customize the display of the captcha elements by adjusting the settings in Settings.yaml.

```yaml
NextBox:
  Neos:
    FormCaptcha:
      # allowed characters for the captcha elements
      possibleLetters: 'bcdfghjkmnpqrstvwxyz23456789'
      image:
        # captcha image width
        width: 130
        # captcha image height
        height: 50
      # number of characters displayed in the captcha image
      totalCharacters: 6
      random:
        # number of points displayed in the captcha image
        dots: 50
        # number of lines displayed in captcha image
        lines: 25
      # font color of the text
      textColor:
        r: 20
        g: 40
        b: 100
      # color of noise
      noiseColor:
        r: 20
        g: 40
        b: 100
      # background color of the Captcha image
      backgroundColor:
        r: 255
        g: 255
        b: 255
```

## i18n

Currently, the following languages are supported: English, German, Danish, Spanish, Finnish, French, Latvian, Dutch and norwegian Bokmal.

Feel free to send us labels in other languages so we can add them to the plugin.