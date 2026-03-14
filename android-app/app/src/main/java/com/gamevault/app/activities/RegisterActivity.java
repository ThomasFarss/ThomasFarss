package com.gamevault.app.activities;

import android.os.Bundle;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.gamevault.app.R;
import com.gamevault.app.services.AuthService;
import com.gamevault.app.utils.ValidationUtils;

public class RegisterActivity extends AppCompatActivity {
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_register);

        EditText name = findViewById(R.id.editName);
        EditText email = findViewById(R.id.editEmail);
        EditText password = findViewById(R.id.editPassword);
        Button register = findViewById(R.id.buttonRegister);

        AuthService authService = new AuthService();
        register.setOnClickListener(v -> {
            if (!ValidationUtils.isRequired(name.getText().toString())) {
                name.setError(getString(R.string.required_field));
                return;
            }
            if (!ValidationUtils.isValidEmail(email.getText().toString())) {
                email.setError(getString(R.string.invalid_email));
                return;
            }
            if (!ValidationUtils.isStrongPassword(password.getText().toString())) {
                password.setError(getString(R.string.invalid_password));
                return;
            }
            authService.register(name.getText().toString(), email.getText().toString(), password.getText().toString())
                    .addOnSuccessListener(r -> {
                        Toast.makeText(this, R.string.register_success, Toast.LENGTH_LONG).show();
                        finish();
                    })
                    .addOnFailureListener(e -> Toast.makeText(this, e.getMessage(), Toast.LENGTH_LONG).show());
        });
    }
}
