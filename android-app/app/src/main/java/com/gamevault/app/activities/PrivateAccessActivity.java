package com.gamevault.app.activities;

import android.os.Bundle;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.gamevault.app.R;

public class PrivateAccessActivity extends AppCompatActivity {
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_private_access);

        EditText password = findViewById(R.id.editAccessPassword);
        Button validate = findViewById(R.id.buttonValidateAccess);

        validate.setOnClickListener(v -> {
            String expected = getIntent().getStringExtra("expectedPassword");
            if (expected != null && expected.equals(password.getText().toString())) {
                setResult(RESULT_OK);
                finish();
            } else {
                Toast.makeText(this, R.string.invalid_folder_password, Toast.LENGTH_LONG).show();
            }
        });
    }
}
