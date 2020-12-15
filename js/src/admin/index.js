import { settings } from "@fof-components";

const {
  SettingsModal,
  items: { BooleanItem, SelectItem, StringItem },
} = settings;

app.initializers.add("askvortsov/saml", () => {
  app.extensionData
    .for("askvortsov-saml")
    .registerSetting(() => (
      <p>
        Make sure that either the metadata url or the metadata is filled in.
      </p>
    ))
    .registerSetting({
      setting: "askvortsov-saml-metadata_url",
      label: app.translator.trans(
        "askvortsov-saml.admin.labels.idp_metadata_url"
      ),
      type: "text",
    })
    .registerSetting({
      setting: "askvortsov-saml-metadata",
      label: app.translator.trans("askvortsov-saml.admin.labels.idp_metadata"),
      type: "text",
    })
    .registerSetting({
      setting: "askvortsov-saml.only_option",
      label: app.translator.trans("askvortsov-saml.admin.labels.only_option"),
      type: "boolean",
    })
    .registerSetting({
      setting: "askvortsov-saml.sync_attributes",
      label: app.translator.trans(
        "askvortsov-saml.admin.labels.sync_attributes"
      ),
      type: "boolean",
    })
    .registerSetting({
      setting: "askvortsov-saml.nameid_format",
      label: app.translator.trans("askvortsov-saml.admin.labels.nameid_format"),
      type: "select",
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
    });
});
