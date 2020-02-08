import { settings } from '@fof-components';

const {
    SettingsModal,
    items: { BooleanItem, StringItem },
} = settings;


app.initializers.add('askvortsov/saml', () => {
    app.extensionSettings['askvortsov-saml'] = () =>
        app.modal.show(
            new SettingsModal({
                title: app.translator.trans('askvortsov-saml.admin.title'),
                type: 'small',
                items: [
                    // IdP Config
                    <StringItem key="askvortsov-saml.idp_metadata" required>
                        {app.translator.trans('askvortsov-saml.admin.labels.idp_metadata')}
                    </StringItem>,
                    // General Info
                    <BooleanItem key="askvortsov-saml.only_option" required>
                        {app.translator.trans('askvortsov-saml.admin.labels.only_option')}
                    </BooleanItem>,
                    // Group Management
                    // <BooleanItem key="askvortsov-saml.sync_attributes" required>
                    //     {app.translator.trans('askvortsov-saml.admin.labels.sync_attributes')}
                    // </BooleanItem>,

                ],
            })
        );
});