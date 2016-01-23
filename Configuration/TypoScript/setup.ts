plugin.tx_formule {

    view {
        templateRootPath = {$plugin.tx_formule.view.templateRootPath}
        partialRootPath = {$plugin.tx_formule.view.partialRootPath}
        layoutRootPath = {$plugin.tx_formule.view.layoutRootPath}
    }

    settings {

        templates {

            1 {
                title = Contact form
                path = EXT:formule/Resources/Private/Standalone/ContactForm.html

                # Load custom assets
                #asset {
                #
                #    0 {
                #        path = EXT:formule/Resources/Public/Build/StyleSheets/formule.css
                #        type = css
                #
                #        # Optional key if loading assets through EXT:vhs.
                #        dependencies = mainCss
                #    }
                #
                #    1 {
                #        path = EXT:formule/Resources/Public/Build/JavaScript/formule.js
                #        type = js
                #
                #        # Optional key if loading assets through EXT:vhs.
                #        dependencies = mainJs
                #    }
                #}
            }

            2 {
                title = Newsletter subscription new
                path = EXT:formule/Resources/Private/Standalone/NewsletterSubscription.html

                validators {
                    0 = Fab\Formule\Processor\EmailValidator
                }

                # Persist configuration
                persist {
                    tableName = fe_users

                    defaultValues {
                        pid = 1
                        disable = 1
                    }

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

            3 {
                title = Newsletter subscription edit
                path = EXT:formule/Resources/Private/Standalone/NewsletterPreferences.html

                loaders {
                    0 = Fab\Formule\Loader\UserDataLoader
                }

                # Persist configuration
                persist {
                    tableName = fe_users

                    processors {
                        0 = Fab\Formule\Processor\UserDataProcessor
                    }
                }
            }
        }

        defaultMappings {

            fe_users {

                # Left value corresponds to name in the form: name="firstName"
                # Right value corresponds to field name: fe_users.first_name
                #firstName = first_name
            }
        }

        loadAssetWithVhsIfAvailable = 1
    }
}
