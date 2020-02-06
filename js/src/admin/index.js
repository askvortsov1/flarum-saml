import { settings } from '@fof-components';

const {
    SettingsModal,
    items: { SelectItem, StringItem },
} = settings;

const modeOptions = { 'off': "Disabled", 'on': "Enabled Alongside Other Auth", 'strictOn': "Enabled, Only Auth Option" }
const groupOptions = { 'off': "Disabled", 'on': "Enabled, Fail Gracefully", 'strictOn': "Enabled, Forced" }

app.initializers.add('askvortsov/saml', () => {
    app.extensionSettings['askvortsov-saml'] = () =>
        app.modal.show(
            new SettingsModal({
                title: app.translator.trans('askvortsov-saml.admin.title'),
                type: 'small',
                items: [
                    // General Info
                    [
                        <label>{ app.translator.trans('askvortsov-saml.admin.labels.mode') }</label>,
                        <SelectItem key="askvortsov-saml.mode" options={modeOptions} required />,
                    ],
                    <StringItem key="askvortsov-saml.sp_name" required>
                        {app.translator.trans('askvortsov-saml.admin.labels.sp_name')}
                    </StringItem>,
                    // IdP Config
                    <StringItem key="askvortsov-saml.idp_endpoint" type="url" required>
                        {app.translator.trans('askvortsov-saml.admin.labels.idp_entity_id')}
                    </StringItem>,
                    <StringItem key="askvortsov-saml.idp_metadata" required>
                        {app.translator.trans('askvortsov-saml.admin.labels.idp_metadata')}
                    </StringItem>,
                    // Group Management
                    [
                        <label>{app.translator.trans('askvortsov-saml.admin.labels.manage_groups')}</label>,
                        <SelectItem key="askvortsov-saml.manage_groups" options={groupOptions} required />,
                    ],

                ],
            })
        );
});