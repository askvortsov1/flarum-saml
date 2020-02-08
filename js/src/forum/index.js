import { extend, override } from 'flarum/extend';
import app from 'flarum/app';
import SettingsPage from "flarum/components/SettingsPage";
import LogInButtons from 'flarum/components/LogInButtons';
import LogInModal from "flarum/components/LogInModal";
import SignUpModal from "flarum/components/SignUpModal";
import LogInButton from 'flarum/components/LogInButton';

app.initializers.add('askvortsov/saml', () => {
    override(LogInModal.prototype, 'body', dontShowLoginModalIfOnlySaml);
    override(SignUpModal.prototype, 'body', dontShowSignupModalIfOnlySaml);
    extend(LogInButtons.prototype, 'items', addSamlLoginButton);

    extend(SettingsPage.prototype, 'accountItems', removeProfileActions);
    extend(SettingsPage.prototype, 'settingsItems', checkRemoveAccountSection);

    function dontShowLoginModalIfOnlySaml() {
        if (app.forum.attribute('onlyUseSaml')) {
            return "See Popup to Login";
        } else {
            return [
                <LogInButtons />,

                <div className="Form Form--centered">
                    {this.fields().toArray()}
                </div>
            ];
        }
    }

    function dontShowSignupModalIfOnlySaml() {
        if (app.forum.attribute('onlyUseSaml') && (jQuery.isEmptyObject(this.props) || this.props.username == "" && this.props.password == "")) {
            return "See Popup to Register";
        } else {
            console.log(this.props);
            return [
                this.props.token ? '' : <LogInButtons />,

                <div className="Form Form--centered">
                    {this.fields().toArray()}
                </div>
            ];
        }
    }

    function addSamlLoginButton(items) {
        items.add('saml',
            <LogInButton
                className="Button LogInButton--saml"
                icon="fas fa-lock"
                path="/auth/saml/login">
                {app.translator.trans('askvortsov-saml.forum.log_in.with_saml_button')}
            </LogInButton>
        );
    };

    function removeProfileActions(items) {
        items.remove('changeEmail');
        items.remove('changePassword');
    }
    function checkRemoveAccountSection(items) {
        if (items.has('account') &&
            items.get('account').props.children.length === 0) {
            items.remove('account');
        }
    }
});

$(function () {
    $('.item-logIn>button').on("click", function (e) {
        if (app.forum.attribute('onlyUseSaml')) {
            window.open("/auth/saml/login", "_blank", "height=500,width=600,resizable=no,toolbar=no,menubar=no,location=no,status=no")
        }
    });
    $('.item-signUp>button').on("click", function (e) {
        if (app.forum.attribute('onlyUseSaml')) {
            window.open("/auth/saml/login", "_blank", "height=500,width=600,resizable=no,toolbar=no,menubar=no,location=no,status=no")
        }
    });
});