package com.gamevault.app.activities;

import android.os.Bundle;
import android.widget.TextView;

import androidx.appcompat.app.AppCompatActivity;

import com.gamevault.app.R;
import com.gamevault.app.services.AuthService;
import com.gamevault.app.services.FirestoreService;

public class UserDownloadsActivity extends AppCompatActivity {
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_user_downloads);

        TextView text = findViewById(R.id.textMyDownloads);
        String uid = new AuthService().currentUser() != null ? new AuthService().currentUser().getUid() : "";
        new FirestoreService().getDownloadLogs().whereEqualTo("userId", uid).get().addOnSuccessListener(snapshot ->
                text.setText(getString(R.string.my_download_count, snapshot.size())));
    }
}
