package com.gamevault.app.activities;

import android.os.Bundle;
import android.widget.TextView;

import androidx.appcompat.app.AppCompatActivity;

import com.gamevault.app.R;
import com.gamevault.app.services.FirestoreService;

public class PermissionMonitorActivity extends AppCompatActivity {
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_permission_monitor);

        TextView monitorText = findViewById(R.id.textMonitorInfo);
        new FirestoreService().getDownloadLogs().get().addOnSuccessListener(snapshot ->
                monitorText.setText(getString(R.string.total_downloads, snapshot.size())));
    }
}
