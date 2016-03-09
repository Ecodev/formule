plugin.tx_formule {

    view {
        templateRootPaths {
            10 = {$plugin.tx_formule.view.templateRootPath}
        }
        partialRootPaths {
            10 = {$plugin.tx_formule.view.partialRootPath}
        }
        layoutRootPaths {
            10 = {$plugin.tx_formule.view.layoutRootPath}
        }
    }

    settings {

        templates {

            1 {
                title = LLL:EXT:formule/Resources/Private/Language/locallang.xlf:contact.form
                path = EXT:formule/Resources/Private/Standalone/ContactForm.html

                validators {
                    0 = Fab\Formule\Validator\EmailFormatValidator
                }

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
                title = LLL:EXT:formule/Resources/Private/Language/locallang.xlf:newsletter.new
                path = EXT:formule/Resources/Private/Standalone/Newsletter/SubscriptionNew.html

                validators {
                    0 = Fab\Formule\Validator\EmailUniqueValidator
                    1 = Fab\Formule\Validator\EmailFormatValidator
                }

                # Variable to be used across the template.
                variable {
                    preferencesPageUid = 1
                }

                # Persist configuration
                persist {
                    tableName = fe_users
                    identifierField = token

                    defaultValues {
                        pid = 1
                        disable = 1
                    }

                    processors {
                        0 = Fab\Formule\Processor\UserDataProcessor

                        # Processor example to handle file upload.
                        # ProTip: you can integrate <fo:message.upload/> in your HTML template.
                        # 1 = Fab\Formule\Processor\PdfUploadProcessor


                        # Processor example to flush cache on certain pages after a database change.
                        # 2 = Fab\Formule\Processor\CacheHandler
                    }

                    mappings {
                        # Left value corresponds to name in the form: name="firstName"
                        # Right value corresponds to field name: fe_users.first_name
                        #first_name = first_name
                    }
                }
            }

            3 {
                title = LLL:EXT:formule/Resources/Private/Language/locallang.xlf:newsletter.edit
                path = EXT:formule/Resources/Private/Standalone/Newsletter/SubscriptionEdit.html

                loaders {
                    0 = Fab\Formule\Loader\UserDataLoader
                }

                validators {
                    0 = Fab\Formule\Validator\EmailUniqueValidator
                    1 = Fab\Formule\Validator\EmailFormatValidator
                    2 = Fab\Formule\Validator\NameValidator
                }

                # Persist configuration
                persist {
                    tableName = fe_users

                    processors {
                        0 = Fab\Formule\Processor\UserDataProcessor
                    }
                }

                redirect {
                    action = show
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
        excludedFieldsFromTemplateParsing = values

        # Tell how to encode email body by default, could be "text" or "html"
        # If set "html", email will be multiparted.
        # If set "text", email will be plain text only.
        preferEmailBodyEncoding = html
    }
}
