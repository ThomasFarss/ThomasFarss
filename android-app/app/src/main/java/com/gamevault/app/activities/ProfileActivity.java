package com.gamevault.app.activities;

import android.content.Intent;
import android.os.Bundle;
import android.widget.TextView;

import androidx.appcompat.app.AppCompatActivity;

import com.gamevault.app.R;
import com.gamevault.app.services.AuthService;
import com.gamevault.app.utils.SessionManager;

public class ProfileActivity extends AppCompatActivity {
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_profile);

        TextView email = findViewById(R.id.textProfileEmail);
        if (new AuthService().currentUser() != null) {
            email.setText(new AuthService().currentUser().getEmail());
        }

        findViewById(R.id.buttonMyDownloads).setOnClickListener(v -> startActivity(new Intent(this, UserDownloadsActivity.class)));
        findViewById(R.id.buttonLogout).setOnClickListener(v -> {
            new AuthService().logout();
            new SessionManager(this).clear();
            Intent i = new Intent(this, LoginActivity.class);
            i.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
            startActivity(i);
        });
    }
}
