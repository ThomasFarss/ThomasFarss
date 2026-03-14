package com.gamevault.app.activities;

import android.os.Bundle;
import android.widget.TextView;

import androidx.appcompat.app.AppCompatActivity;

import com.gamevault.app.R;
import com.gamevault.app.services.FirestoreService;

public class UserManagementActivity extends AppCompatActivity {
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_user_management);

        TextView info = findViewById(R.id.textUsersInfo);
        new FirestoreService().getAllUsers().get().addOnSuccessListener(snapshot ->
                info.setText(getString(R.string.total_users, snapshot.size())));
    }
}
