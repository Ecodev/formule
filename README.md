Formule for TYPO3 CMS
=====================

Render a variety of forms template based such as contact form, registration form, etc... effortless!

Consider these minimum steps to render the form:

* Use a form generator using some tools online. The choice is your's
* Paste the generated code in an HTML template. 
* Add some TypoScript configuration where to find this HTML template. Optionnaly tell if the data should be persisted
* Your form is basically ready. 


Project info and releases
-------------------------

<!--Stable version:-->
<!--http://typo3.org/extensions/repository/view/formule-->

Development version:
https://github.com/Ecodev/formule

::

	git clone https://github.com/Ecodev/formule.git

News about latest development are also announced on http://twitter.com/fudriot

Installation and requirement
============================

The extension **requires TYPO3 6.2 or greater** . Install the extension as normal in the Extension Manager from the `TER`_ or download the Git version::

	# local installation
	cd typo3conf/ext

	# download the source
	git clone https://github.com/Ecodev/formule.git

	-> next step, is to open the Extension Manager in the BE.

.. _TER: http://typo3.org/extensions/repository/view/formule

You are almost there! Create a Content Element of type "Vidi Frontend" in `General Plugin` > `Generic List Component` and configure at your convenience.

.. image:: https://raw.github.com/Ecodev/formule/master/Documentation/Backend-01.png

Configuration
=============

The plugin can be configured in various places such as TypoScript, PHP or in the plugin record itself.

**Important** by default, the CSS + JS files are loaded for Bootstrap. For a more Vanilla flavor, edit the `path` in the `settings` key in TypoScript and
load the right assets for you. See below the comments::

	#############################
	# plugin.tx_formule
	#############################
	plugin.tx_formule {

		settings {

			asset {

				vidiCss {
					# For none Bootstrap replace "formule.bootstrap.min.css" by "formule.min.css"
					path = EXT:formule/Resources/Public/Build/StyleSheets/formule.bootstrap.min.css
					type = css
				}

				vidiJs {
					# For none Bootstrap replace "formule.bootstrap.min.js" by "formule.min.js"
					path = EXT:formule/Resources/Public/Build/JavaScript/formule.bootstrap.min.js
					type = js
				}
			}
		}
	}

Fields control
--------------

* The extension ships a honeypot controller
* declare fields as required

Persist data
------------


Register a new template
-----------------------

The detail view of the content can be personalized per plugin record. To register more templates, simply define them in your TypoScript configuration.
This TypoScript will typically be put under within ``EXT:foo/Configuration/TypoScript/setup.txt``::

	plugin.tx_formule {
		settings {
			templates {

				# Key "1", "2" is already taken by the extension.
				# Use key "10", "11" and following for your own templates to be safe.
				10 {
					title = Foo detail view
					path = EXT:foo/Resources/Private/Templates/formule/ShowFoo.html
					
				}
			}
		}
	}
