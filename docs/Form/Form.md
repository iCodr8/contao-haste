# Haste Form

About
-----

Haste Form has been designed to ease working with Contao forms in the front end.


Examples
------------

A lot of the following examples can be combined.
For more internal details please read the source ;-)

### Preparing a form instance
```php
<?php

    // First param is the form id
    // Second is either GET or POST
    // Third is a callable that decides when your form is submitted
    // You can pass an optional fourth parameter (true by default) to turn the form into a table based one
    $objForm = new \Haste\Form\Form('someid', 'POST', function($haste) {
        return \Input::post('FORM_SUBMIT') === $haste->getFormId();
    });

    // Haste will never decide for you when the form has been submitted.
    // You have to tell it! Let's have a look at an example using GET
    // Haste will turn into the submitted state as soon as the GET param
    // "foo" contains the value "bar"
    $objForm = new \Haste\Form\Form('someid', 'GET', function() {
        return \Input::get('foo') === 'bar';
    });

    // You can optionally preserve the current GET parameters.
    // They will be added as hidden fields to the current form.
    // This is especially useful when using multiple GET forms (like search and filter).
    $objForm->preserveGetParameters();
    $objForm->preserveGetParameters(array('page_n')); // Exclude 'page_n' parameter

    // A form needs an action. By default it's the current request URI you
    // place your Haste form on, but you can either set your own URI:
    $objForm->setFormActionFromUri('https://foo.bar/somewhere.html');

    // Or you can pass a page ID that Haste will turn into an URI for your
    // convenience:
    $objForm->setFormActionFromPageId(42);

    // Now let's add form fields:
    $objForm->addFormField('year', array(
        'label'         => 'Year',
        'inputType'     => 'text',
        'eval'          => array('mandatory'=>true, 'rgxp'=>'digit')
    ));

    // Need a checkbox?
    $objForm->addFormField('termsOfUse', array(
        'label'         => array('This is the <legend>', 'This is the <label>'),
        'inputType'     => 'checkbox',
        'eval'          => array('mandatory'=>true)
    ));

    // Let's add  a submit button
    $objForm->addFormField('submit', array(
      'label'     => 'Submit form',
      'inputType' => 'submit'
    ));

    // For the ease of use we do provide two helpers for the submit button and captcha field
    $objForm->addSubmitFormField('submit', 'Submit form');
    $objForm->addCaptchaFormField('captcha');

```

### Generating the form
Now that you have your form instance ready, you can generate the markup for it
and validate the user inputs etc.

```php
<?php

    // validate() also checks whether the form has been submitted
    if ($objForm->validate()) {

        // Get the submitted and parsed data of a field (only works with POST):
        $arrData = $objForm->fetch('year');

        // Get all the submitted and parsed data (only works with POST):
        $arrData = $objForm->fetchAll();

        // For your convenience you can also use a callable to walk over all widgets
        $arrData = $objForm->fetchAll(function($strName, $objWidget) {
            return \Input::postRaw($strName);
        });

        // Read from POST: \Input::post('year');
        // Read from GET: \Input::get('year');
    }

    // Get the form as string
    echo $objForm->generate();
    // or just
    echo $objForm;

    // You can also pass your own Template instance
    $objMyTemplate = new \FrontendTemplate('mytemplate');
    $objForm->addToTemplate($objMyTemplate);
    echo $objMyTemplate->parse();
```

### Add the form fields from a back end DCA

```php
<?php
    // you can exclude or modify certain fields by passing a callable as second
    // parameter and return your modified fields array
    $objForm->addFieldsFromDca('tl_content', function($arrFields) {
        unset($arrFields['idontwantyou']);
        return $arrFields;
    });
```

### Add the form fields from a form generator form ID

```php
<?php
    // you can exclude or modify certain fields by passing a callable as second
    // parameter
    $objForm->addFieldsFromFormGenerator(42, function(&$arrFields) {
        unset($arrFields['idontwantyou']);
    });
```

### Removing fields on a form instance

```php
<?php
    $objForm->removeFormField('firstname');
```

### Bind models to the form
You can easily bind a Contao Model instance to the form to ease working with them.
Haste form will try to load and preset the data from the model if there is already
some and will also store the values to the model.
However, it will *not* call `->save()` on the model so you can still do with it
whatever you like.

```php
<?php
    // Presets values from page ID 42
    $objModel = \PageModel::findByPk(42);
    $objForm->bindModel($objModel);

    $objForm->addFieldsFromDca('tl_page');

    if ($objForm->validate()) {
        // The model will now contain the changes so you can save it
        $objModel->save();
    }
```