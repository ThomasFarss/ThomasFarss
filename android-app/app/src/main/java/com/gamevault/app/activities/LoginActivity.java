package com.gamevault.app.activities;

import android.content.Intent;
import android.os.Bundle;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.gamevault.app.R;
import com.gamevault.app.services.AuthService;
import com.gamevault.app.utils.SessionManager;
import com.gamevault.app.utils.ValidationUtils;

public class LoginActivity extends AppCompatActivity {
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);

        EditText email = findViewById(R.id.editEmail);
        EditText password = findViewById(R.id.editPassword);
        Button loginButton = findViewById(R.id.buttonLogin);
        TextView createAccount = findViewById(R.id.textCreateAccount);
        TextView forgotPassword = findViewById(R.id.textForgotPassword);

        AuthService authService = new AuthService();
        SessionManager sessionManager = new SessionManager(this);

        loginButton.setOnClickListener(v -> {
            if (!ValidationUtils.isValidEmail(email.getText().toString())) {
                email.setError(getString(R.string.invalid_email));
                return;
            }
            if (!ValidationUtils.isStrongPassword(password.getText().toString())) {
                password.setError(getString(R.string.invalid_password));
                return;
            }

            authService.login(email.getText().toString(), password.getText().toString())
                    .addOnSuccessListener(r -> {
                        if (authService.currentUser() != null) {
                            sessionManager.saveSession(authService.currentUser().getUid(), false);
                        }
                        startActivity(new Intent(this, MainActivity.class));
                        finish();
                    })
                    .addOnFailureListener(e -> Toast.makeText(this, e.getMessage(), Toast.LENGTH_LONG).show());
        });

        createAccount.setOnClickListener(v -> startActivity(new Intent(this, RegisterActivity.class)));
        forgotPassword.setOnClickListener(v -> startActivity(new Intent(this, ForgotPasswordActivity.class)));
    }
}
