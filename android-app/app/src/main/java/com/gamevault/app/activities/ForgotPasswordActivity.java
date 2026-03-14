package com.gamevault.app.activities;

import android.os.Bundle;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.gamevault.app.R;
import com.gamevault.app.services.AuthService;
import com.gamevault.app.utils.ValidationUtils;

public class ForgotPasswordActivity extends AppCompatActivity {
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_forgot_password);

        EditText email = findViewById(R.id.editEmail);
        Button send = findViewById(R.id.buttonSendReset);

        send.setOnClickListener(v -> {
            if (!ValidationUtils.isValidEmail(email.getText().toString())) {
                email.setError(getString(R.string.invalid_email));
                return;
            }
            new AuthService().forgotPassword(email.getText().toString())
                    .addOnSuccessListener(r -> Toast.makeText(this, R.string.reset_sent, Toast.LENGTH_LONG).show())
                    .addOnFailureListener(e -> Toast.makeText(this, e.getMessage(), Toast.LENGTH_LONG).show());
        });
    }
}
