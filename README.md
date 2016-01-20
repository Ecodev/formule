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

![](https://raw.github.com/Ecodev/formule/master/Documentation/Backend-01.png)

Configuration
=============

The plugin can be configured in various places such as TypoScript, PHP or in the plugin record itself.

In the Extension Manager possible email redirections according to the Application Context. This maybe useful for debugging.


Register a new template
-----------------------

By default we ship a limited set of forms. So it is very likely you want to have your own ones according to your needs. Consider adding some TypoScript. Here is the minimum settings, typically put in ``EXT:foo/Configuration/TypoScript/setup.txt``:

```

	plugin.tx_formule {
		settings {
			templates {

				# Key "1", "2" is already taken by the extension.
				# Use key "10", "11" and following for your own templates to be safe.
				10 {
					title = Foo detail view
					path = EXT:foo/Resources/Private/Templates/formule/MailForm.html
					
				}
			}
		}
	}
```

Here is a more complex example which will load additional JS / CSS plus add configuration to persist the submitted data into the database.

```

    plugin.tx_formule {
    
        settings {
    
            templates {
    
				# Key "1", "2" is already taken by the extension.
				# Use key "10", "11" and following for your own templates to be safe.
                11 {
                    title = Newsletter subscription new
                    path = EXT:foo/Resources/Private/Standalone/Newsletter/NewSubscription.html
    
                    # Persist configuration
                    persist {
                        tableName = fe_users
    
                        defaultValues {
                            pid = 1
                            disable = 1
                        }
    
                        processors {
                            0 = Vendor\Formule\Processor\UserDataProcessor
                        }
    
                        mappings {
                            # Left value corresponds to name in the form: name="firstName"
                            # Right value corresponds to field name: fe_users.first_name
                            #first_name = first_name
                        }
                    }
                    
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
            }
        }
    }

```

HTML template
-------------

Important to notice, Formule will read the template file and analyse its content to extract allowed fields and mandatory values.
There are some minimum mandatory values to load.

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

Fields control
--------------

* The extension ships a honeypot View Helper which reduce the bot annoyance
* The fields marked as `required="required"` will be controlled as such
* Todo: we could introduce the `pattern=""` (not yet implemented)
