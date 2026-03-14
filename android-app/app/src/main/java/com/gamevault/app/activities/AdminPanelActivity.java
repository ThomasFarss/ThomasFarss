package com.gamevault.app.activities;

import android.content.Intent;
import android.os.Bundle;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.gamevault.app.R;
import com.gamevault.app.utils.SessionManager;

public class AdminPanelActivity extends AppCompatActivity {
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        if (!new SessionManager(this).isAdminSession()) {
            Toast.makeText(this, R.string.admin_only_area, Toast.LENGTH_LONG).show();
            finish();
            return;
        }

        setContentView(R.layout.activity_admin_panel);
        findViewById(R.id.buttonManageUsers).setOnClickListener(v -> startActivity(new Intent(this, UserManagementActivity.class)));
        findViewById(R.id.buttonManageGroups).setOnClickListener(v -> startActivity(new Intent(this, GroupManagementActivity.class)));
        findViewById(R.id.buttonPermissions).setOnClickListener(v -> startActivity(new Intent(this, PermissionMonitorActivity.class)));
    }
}
