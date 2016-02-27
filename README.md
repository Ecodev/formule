Formule for TYPO3 CMS
=====================

Template based, render a variety of forms such as contact form, registration form, etc... effortless!

Consider these minimum steps to display a form and start submitting data:

* Create a content element of type "formule" in the Backend.
* Add some TypoScript configuration to declare a new HTML template.
* Adjust your template in particular the form. Use a form generator of your choice.
* Your form is basically ready. 

Project info and releases
-------------------------

<!--Stable version:-->
<!--http://typo3.org/extensions/repository/view/formule-->

Development version:
https://github.com/Ecodev/formule

	git clone https://github.com/Ecodev/formule.git

News about latest development are also announced on http://twitter.com/fudriot

Installation and requirement
============================

The extension **requires TYPO3 6.2 or greater**. Install the extension as normal in the Extension Manager from the TER (to be released) or download via Composer:

```

	"require": {
	    "fab/formule": "dev-master",
	}

	-> next step, is to open the Extension Manager in the BE.
```

You are almost there! Create a Content Element of type "formule" in `General Plugin` > `Variety of forms` and configure at your convenience.

![](https://raw.githubusercontent.com/Ecodev/formule/master/Documentation/Backend-01.png)

Configuration
=============

The plugin can be configured mainly in TypoScript. In the Extension Manager possible email redirection can be set according to the Application Context. This maybe useful when developing to avoid sending email to the final user.

Register a new template
-----------------------

By default the extension provides a limited set of forms: a basic mail form along with a newsletter registration example. It is very likely you want to add new ones. To register a new template and see it in the plugin record, consider the two necessary steps:

* Create a new HTML template `EXT:foo/Resources/Private/Plugins/Formule/MyForm.html`. You can take inspiration from the one in `EXT:formule`.
* Add some minimum TypoScript settings, e.g. in `EXT:foo/Configuration/TypoScript/Plugin/tx_formule.ts`

```

	plugin.tx_formule {
		settings {
			templates {

				# Key "1", "2" is already taken by the extension.
				# Use key "10", "11" and following for your own templates to be safe.
				10 {
					title = Foo detail view
					path = EXT:foo/Resources/Private/Templates/formule/ContactForm.html
					
				}
			}
		}
	}
```


Load additional assets
----------------------

Below is a more complex example which will load additional JS / CSS. 
This TypoScript could be written placed in a file, e.g. in `EXT:foo/Configuration/TypoScript/Plugin/tx_formule.ts`

```

    plugin.tx_formule.settings.template.11 {
    
        title = Newsletter subscription new
        path = EXT:foo/Resources/Private/Standalone/Newsletter/NewSubscription.html

        # Load custom assets
        asset {
        
            0 {
                path = EXT:foo/Resources/Public/Build/StyleSheets/formule.css
                type = css
        
                # Optional key if loading assets through EXT:vhs.
                dependencies = mainCss
            }
        
            1 {
                path = EXT:foo/Resources/Public/Build/JavaScript/formule..js
                type = js
        
                # Optional key if loading assets through EXT:vhs.
                dependencies = mainJs
            }
        }
    }

```

Persist to the database
-----------------------

One can also set a configuration to persist submitted data into the database.
This TypoScript could be written placed in a file, e.g. in `EXT:foo/Configuration/TypoScript/Plugin/tx_formule.ts`

```

    plugin.tx_formule.settings.template.11 {
    
        title = Newsletter subscription new
        path = EXT:foo/Resources/Private/Standalone/Newsletter/NewSubscription.html

        # Persist configuration
        persist {
            tableName = fe_users

            defaultValues {
                pid = 1
                disable = 1
            }

            # Possibly process the values
            processors {
                0 = Fab\Formule\Processor\UserDataProcessor
            }

            mappings {
                # Left value corresponds to name in the form: name="firstName"
                # Right value corresponds to field name: fe_users.first_name
                #first_name = first_name
            }
        }
    }

```

Loading data
------------

To pre-load data and inject values in the form, one can configure loaders. A Loader corresponds to a PHP class where one can fetch some data and return an array of values.

```

    plugin.tx_formule.settings.template.11 {
    
        title = Newsletter subscription new
        path = EXT:foo/Resources/Private/Standalone/Newsletter/NewSubscription.html

        loaders {
            0 = Fab\Formule\Loader\UserDataLoader
        }

    }

```

HTML template
-------------

The template has the bare minimum requirements. A Fluid form must be declared sending its content to action "submit". It is has one required field to retrieve the original Content element configuration `<f:form.hidden name="values" value="{contentElement.uid}"/>`. Formule has a mechanism to read and analyse the content. From that, it will extract allowed fields and mandatory values. Notice the basic structure with the inline comments.

```
    
    <f:form action="submit" controller="Form" additionalAttributes="{role: 'form'}" method="post">
    
        <div class="form-group">
            <input type="text"
                   class="form-control"
                   id="name"
                   name="name"
                   value="{values.name}"
                   placeholder="{f:translate(key:'name')}"
                   required="required"/>
        </div>

        <input type="submit"/>

        <!-- The only mandatory field -->
        <f:form.hidden name="values" value="{contentElement.uid}"/>

        <!-- VH to limit bots annoyance (required) -->
        <fo:honeyPot/>

        <!--Display hint in Development context (optional) -->
        <fo:message.development/>
    </f:form>
```

Sections in template
--------------------

The template can be 


```

    # Required section
    <f:section name="main">
        Content of the template
    </f:section>
```

This section is optional and is to define the body part of the email to the admin. If present if will replace the value from the flexform.


```

    <f:section name="emailAdmin">
    
    </f:section>
```

Same as above but for the end user. If present if will replace the value from the flexform.

```

    <f:section name="emailUser">
    
    </f:section>
```

This section is optional and is to define the feedback message for the end user after successfully submitting the form.

```

    <f:section name="feedback">
    
    </f:section>
```

Template variable
------------------

For convenience sake, the extension provides a bunch of global variables than can be used across emails (subject or body part):

* {HTTP_HOST} : www.example.org
* {HTTP_REFERER} : http://www.example.org/example/

Fields control
--------------

* The extension ships a honeypot View Helper to reduce bot annoyances.
* The fields marked as `required="required"` will be extracted and controlled as such.
* Todo: we could introduce the HTML5 attribute `pattern=""` for better field control (not yet implemented).
