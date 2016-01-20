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
                path = EXT:formule/Resources/Private/Standalone/MailForm.html

                # Table name where to persist submitted data.

                #asset {
                #
                #    0 {
                #        # For none Bootstrap replace by EXT:formule/Resources/Public/Build/StyleSheets/formule.min.css
                #        path = EXT:formule/Resources/Public/Build/StyleSheets/formule.bootstrap.min.css
                #        type = css
                #
                #        # Optional key if loading assets through EXT:vhs.
                #        dependencies = mainCss
                #    }
                #
                #    1 {
                #        # For none Bootstrap replace by EXT:formule/Resources/Public/Build/JavaScript/formule.min.js
                #        path = EXT:formule/Resources/Public/Build/JavaScript/formule.bootstrap.min.js
                #        type = js
                #
                #        # Optional key if loading assets through EXT:vhs.
                #        dependencies = mainJs
                #    }
                #}
            }

            2 {
                title = Newsletter subscription new
                path = EXT:formule/Resources/Private/Standalone/Newsletter/NewSubscription.html

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
                path = EXT:formule/Resources/Private/Standalone/SubscribeEditForm.html
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
