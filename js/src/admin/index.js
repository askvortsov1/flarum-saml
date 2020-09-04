import { settings } from "@fof-components";

const {
  SettingsModal,
  items: { BooleanItem, SelectItem, StringItem },
} = settings;

app.initializers.add("askvortsov/saml", () => {
  app.extensionSettings["askvortsov-saml"] = () =>
    app.modal.show(SettingsModal, {
      title: app.translator.trans("askvortsov-saml.admin.title"),
      type: "small",
      items: (s) => [
        <p>
          Make sure that either the metadata url or the metadata is filled in.
        </p>,
        <StringItem
          name="askvortsov-saml.idp_metadata_url"
          id="askvortsov-saml-metadata_url"
          setting={s}
        >
          {app.translator.trans(
            "askvortsov-saml.admin.labels.idp_metadata_url"
          )}
        </StringItem>,
        <StringItem
          name="askvortsov-saml.idp_metadata"
          id="askvortsov-saml-metadata"
          setting={s}
        >
          {app.translator.trans("askvortsov-saml.admin.labels.idp_metadata")}
        </StringItem>,
        <BooleanItem name="askvortsov-saml.only_option" setting={s} required>
          {app.translator.trans("askvortsov-saml.admin.labels.only_option")}
        </BooleanItem>,
        <BooleanItem
          name="askvortsov-saml.sync_attributes"
          setting={s}
          required
        >
          {app.translator.trans("askvortsov-saml.admin.labels.sync_attributes")}
        </BooleanItem>,
        <div className="Form-group">
          <label>
            {app.translator.trans("askvortsov-saml.admin.labels.nameid_format")}
          </label>

          {SelectItem.component({
            options: {
              "urn:oasis:names:tc:SAML:2.0:nameid-format:persistent": app.translator.trans(
                "askvortsov-saml.admin.options.nameid_format.persistent"
              ),
              "urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress": app.translator.trans(
                "askvortsov-saml.admin.options.nameid_format.emailAddress"
              ),
              "urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified": app.translator.trans(
                "askvortsov-saml.admin.options.nameid_format.unspecified"
              ),
            },
            name: "askvortsov-saml.nameid_format",
            setting: s,
            required: true,
          })}
        </div>,
      ],
    });
});
