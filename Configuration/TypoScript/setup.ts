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
                tableName = ''

                asset {
                    vidiCss {
                        # For none Bootstrap replace by EXT:formule/Resources/Public/Build/StyleSheets/formule.min.css
                        path = EXT:formule/Resources/Public/Build/StyleSheets/formule.bootstrap.min.css
                        type = css

                        # Optional key if loading assets through EXT:vhs.
                        dependencies = mainCss
                    }

                    vidiJs {
                        # For none Bootstrap replace by EXT:formule/Resources/Public/Build/JavaScript/formule.min.js
                        path = EXT:formule/Resources/Public/Build/JavaScript/formule.bootstrap.min.js
                        type = js

                        # Optional key if loading assets through EXT:vhs.
                        dependencies = mainJs
                    }
                }

#                finishers {
#                    tableName = fe_users
#                }
            }

            2 {
                # Restrict visibility of this template for "fe_users" only.
                title = Newsletter subscription create
                path = EXT:formule/Resources/Private/Standalone/SubscribeCreateForm.html
                tableName = 'fe_users'
            }

            3 {
                # Restrict visibility of this template for "fe_users" only.
                title = Newsletter subscription edit
                path = EXT:formule/Resources/Private/Standalone/SubscribeEditForm.html
                tableName = 'fe_users'
            }
        }

        # Fluid variable mappings to be used in the detail view of your Fluid template.
        fluidVariables {
            fe_users = user
        }

        mappings {
            fe_users {

            }
        }

        loadAssetWithVhsIfAvailable = 1
    }
}
