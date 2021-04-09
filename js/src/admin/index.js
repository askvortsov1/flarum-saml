app.initializers.add("askvortsov/saml", () => {
  app.extensionData
    .for("askvortsov-saml")
    .registerSetting(() => (
      <p>{app.translator.trans("askvortsov-saml.admin.header.text")}</p>
    ))
    .registerSetting({
      setting: "askvortsov-saml.idp_metadata_url",
      label: app.translator.trans(
        "askvortsov-saml.admin.labels.idp_metadata_url"
      ),
      type: "text",
    })
    .registerSetting({
      setting: "askvortsov-saml.idp_metadata",
      label: app.translator.trans("askvortsov-saml.admin.labels.idp_metadata"),
      type: "text",
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
    })
    .registerSetting({
      setting: "askvortsov-saml.want_assertions_signed",
      label: app.translator.trans(
        "askvortsov-saml.admin.labels.want_assertions_signed"
      ),
      type: "boolean",
    })
    .registerSetting({
      setting: "askvortsov-saml.want_messages_signed",
      label: app.translator.trans(
        "askvortsov-saml.admin.labels.want_messages_signed"
      ),
      type: "boolean",
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
    });
});
