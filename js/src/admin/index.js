import { settings } from '@fof-components';

const {
    SettingsModal,
    items: { BooleanItem, SelectItem, StringItem },
} = settings;


app.initializers.add('askvortsov/saml', () => {
    app.extensionSettings['askvortsov-saml'] = () =>
        app.modal.show(
            new SettingsModal({
                title: app.translator.trans('askvortsov-saml.admin.title'),
                type: 'small',
                items: [
                    <StringItem key="askvortsov-saml.idp_metadata" required>
                        {app.translator.trans('askvortsov-saml.admin.labels.idp_metadata')}
                    </StringItem>,
                    <BooleanItem key="askvortsov-saml.only_option" required>
                        {app.translator.trans('askvortsov-saml.admin.labels.only_option')}
                    </BooleanItem>,
                    <BooleanItem key="askvortsov-saml.sync_attributes" required>
                        {app.translator.trans('askvortsov-saml.admin.labels.sync_attributes')}
                    </BooleanItem>,
                    <div className="Form-group">
                        <label>{app.translator.trans('askvortsov-saml.admin.labels.nameid_format')}</label>

                        {SelectItem.component({
                            options: {
                                "urn:oasis:names:tc:SAML:2.0:nameid-format:persistent": app.translator.trans('askvortsov-saml.admin.options.nameid_format.persistent'),
                                "urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress": app.translator.trans('askvortsov-saml.admin.options.nameid_format.emailAddress'),
                                "urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified": app.translator.trans('askvortsov-saml.admin.options.nameid_format.unspecified')

                            },
                            key: 'askvortsov-saml.nameid_format',
                            required: true,
                        })}
                    </div>,
                ],
            })
        );
});