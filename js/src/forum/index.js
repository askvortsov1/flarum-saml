import { extend } from 'flarum/extend';
import app from 'flarum/app';
import LogInButtons from 'flarum/components/LogInButtons';
import LogInButton from 'flarum/components/LogInButton';

app.initializers.add('askvortsov/saml', () => {
    extend(LogInButtons.prototype, 'items', function (items) {
        items.add('saml',
            <LogInButton
                className="Button LogInButton--saml"
                icon="fas fa-lock"
                path="/auth/saml/login">
                {app.translator.trans('askvortsov-saml.forum.log_in.with_saml_button')}
            </LogInButton>
        );
    });
});